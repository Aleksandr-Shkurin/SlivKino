// Админ-панель приложение
const AdminApp = {
    // Данные (загружаются из localStorage или из глобальных переменных)
    movies: { nowPlaying: [], comingSoon: [] },
    cinemas: [],
    halls: {},
    
    // Инициализация
    async init() {
        await this.loadData();
        this.renderMovies();
        this.renderCinemas();
        this.renderSessions();
        this.renderBookings();
        this.loadHallCinemaSelect();
        this.loadCinemaSelects();
    },
    
    // Загрузка данных из localStorage или из глобальных переменных
    async loadData() {
        try {
            const response = await fetch("/api/state");
            if (response.ok) {
                const state = await response.json();
                this.movies = state.movies || { nowPlaying: [], comingSoon: [] };
                this.cinemas = state.cinemas || [];
                this.halls = state.halls || {};
                localStorage.setItem("slivkino_movies", JSON.stringify(this.movies));
                localStorage.setItem("slivkino_cinemas", JSON.stringify(this.cinemas));
                localStorage.setItem("slivkino_halls", JSON.stringify(this.halls));
                if (state.bookings) localStorage.setItem("slivkino_bookings", JSON.stringify(state.bookings));
                return;
            }
        } catch (error) { console.warn("API базы данных недоступен, используется локальный режим", error); }
        // Загружаем фильмы
        const savedMovies = localStorage.getItem('slivkino_movies');
        if (savedMovies) {
            try {
                const parsed = JSON.parse(savedMovies);
                this.movies = parsed;
            } catch(e) { console.error('Ошибка загрузки фильмов', e); }
        } else if (typeof moviesDatabase !== 'undefined') {
            this.movies = {
                nowPlaying: [...moviesDatabase.nowPlaying],
                comingSoon: [...moviesDatabase.comingSoon]
            };
            this.saveMovies();
        }
        
        // Загружаем кинотеатры
        const savedCinemas = localStorage.getItem('slivkino_cinemas');
        if (savedCinemas) {
            try {
                this.cinemas = JSON.parse(savedCinemas);
            } catch(e) { console.error('Ошибка загрузки кинотеатров', e); }
        } else if (typeof cinemasDatabase !== 'undefined') {
            this.cinemas = [...cinemasDatabase];
            this.saveCinemas();
        }
        
        // Загружаем залы
        const savedHalls = localStorage.getItem('slivkino_halls');
        if (savedHalls) {
            try {
                this.halls = JSON.parse(savedHalls);
            } catch(e) { console.error('Ошибка загрузки залов', e); }
        } else if (typeof hallsConfig !== 'undefined') {
            this.halls = { ...hallsConfig };
            this.saveHalls();
        }
    },
    
    // Сохранение фильмов
    saveMovies() {
        localStorage.setItem('slivkino_movies', JSON.stringify(this.movies));
        // Обновляем глобальные переменные для других страниц
        if (typeof moviesDatabase !== 'undefined') {
            moviesDatabase.nowPlaying = this.movies.nowPlaying;
            moviesDatabase.comingSoon = this.movies.comingSoon;
        }
        this.persistState();
    },
    
    // Сохранение кинотеатров
    saveCinemas() {
        localStorage.setItem('slivkino_cinemas', JSON.stringify(this.cinemas));
        if (typeof cinemasDatabase !== 'undefined') {
            cinemasDatabase.length = 0;
            cinemasDatabase.push(...this.cinemas);
        }
        this.persistState();
    },
    
    // Сохранение залов
    saveHalls() {
        localStorage.setItem('slivkino_halls', JSON.stringify(this.halls));
        if (typeof hallsConfig !== 'undefined') {
            Object.keys(this.halls).forEach(key => {
                hallsConfig[key] = this.halls[key];
            });
        }
        this.persistState();
    },
    
    // Сохранение всех данных в SQLite через сервер
    async persistState() {
        try {
            const response = await fetch("/api/state", {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    movies: this.movies,
                    cinemas: this.cinemas,
                    halls: this.halls,
                    bookings: this.getBookings()
                })
            });
            if (!response.ok) throw new Error("HTTP " + response.status);
        } catch (error) {
            console.error("Не удалось сохранить данные в БД. Запускайте сайт через npm start.", error);
            if (typeof this.showMessage === "function") this.showMessage("Нет связи с сервером БД. Изменения сохранены только в этом браузере.", "error");
        }
    },

    // Показать таб
    showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`tab-${tabName}`).classList.add('active');
        event.target.classList.add('active');
        
        if (tabName === 'movies') this.renderMovies();
        else if (tabName === 'cinemas') this.renderCinemas();
        else if (tabName === 'halls') this.loadHallsList();
        else if (tabName === 'sessions') this.renderSessions();
        else if (tabName === 'bookings') this.renderBookings();
    },
    
    // ============ ФИЛЬМЫ ============
    renderMovies() {
        const tbody = document.getElementById('movies-list');
        if (!tbody) return;
        
        const allMovies = [...this.movies.nowPlaying, ...this.movies.comingSoon];
        if (allMovies.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Нет фильмов</td></tr>';
            return;
        }
        
        tbody.innerHTML = allMovies.map(movie => `
            <tr>
                <td>${movie.id}</td>
                <td class="poster-cell">${movie.poster ? `<img src="${movie.poster}" onerror="this.style.display='none'">` : '🎬'}</td>
                <td><strong>${movie.title}</strong><br><small>${movie.titleEn || ''}</small></td>
                <td>${movie.genres.map(g => `<span class="genre-badge">${g}</span>`).join(' ')}</td>
                <td>${movie.year}</td>
                <td>${movie.ageRating}</td>
                <td>
                    <button class="edit-btn" onclick="adminApp.editMovie(${movie.id})">✏️</button>
                    <button class="delete-btn" onclick="adminApp.deleteMovie(${movie.id})">🗑️</button>
                </td>
            </tr>
        `).join('');
    },
    
    openMovieModal(movie = null) {
        const modal = document.getElementById('movie-modal');
        modal.classList.add('active');
        
        if (movie) {
            document.getElementById('movie-modal-title').innerText = 'Редактировать фильм';
            document.getElementById('movie-id').value = movie.id;
            document.getElementById('movie-title').value = movie.title || '';
            document.getElementById('movie-title-en').value = movie.titleEn || '';
            document.getElementById('movie-genres').value = (movie.genres || []).join(', ');
            document.getElementById('movie-country').value = movie.country || '';
            document.getElementById('movie-year').value = movie.year || '';
            document.getElementById('movie-age').value = movie.ageRating || '';
            document.getElementById('movie-release').value = this.formatDateForInput(movie.releaseDate);
            document.getElementById('movie-end').value = this.formatDateForInput(movie.endDate);
            document.getElementById('movie-poster').value = movie.poster || '';
            document.getElementById('movie-description').value = movie.description || '';
            document.getElementById('movie-director').value = movie.director || '';
            document.getElementById('movie-cast').value = (movie.cast || []).join(', ');
            document.getElementById('movie-duration').value = movie.duration || '';
        } else {
            document.getElementById('movie-modal-title').innerText = 'Добавить фильм';
            document.getElementById('movie-id').value = '';
            document.getElementById('movie-title').value = '';
            document.getElementById('movie-title-en').value = '';
            document.getElementById('movie-genres').value = '';
            document.getElementById('movie-country').value = '';
            document.getElementById('movie-year').value = '';
            document.getElementById('movie-age').value = '';
            document.getElementById('movie-release').value = '';
            document.getElementById('movie-end').value = '';
            document.getElementById('movie-poster').value = '';
            document.getElementById('movie-description').value = '';
            document.getElementById('movie-director').value = '';
            document.getElementById('movie-cast').value = '';
            document.getElementById('movie-duration').value = '';
        }
    },
    
    saveMovie() {
        const id = document.getElementById('movie-id').value;
        const genresStr = document.getElementById('movie-genres').value;
        const genres = genresStr ? genresStr.split(',').map(g => g.trim().toLowerCase()).filter(g => g) : [];
        const castStr = document.getElementById('movie-cast').value;
        const cast = castStr ? castStr.split(',').map(c => c.trim()).filter(c => c) : [];
        
        const movieData = {
            id: id ? parseInt(id) : Date.now(),
            title: document.getElementById('movie-title').value,
            titleEn: document.getElementById('movie-title-en').value,
            genres: genres,
            country: document.getElementById('movie-country').value,
            year: parseInt(document.getElementById('movie-year').value) || new Date().getFullYear(),
            ageRating: document.getElementById('movie-age').value,
            releaseDate: this.formatDateForSave(document.getElementById('movie-release').value),
            endDate: this.formatDateForSave(document.getElementById('movie-end').value),
            poster: document.getElementById('movie-poster').value,
            description: document.getElementById('movie-description').value,
            fullDescription: document.getElementById('movie-description').value,
            director: document.getElementById('movie-director').value,
            cast: cast,
            duration: document.getElementById('movie-duration').value,
            sessions: []
        };
        
        if (!movieData.title) { this.showMessage('Введите название!', 'error'); return; }
        if (genres.length === 0) { this.showMessage('Добавьте жанры!', 'error'); return; }
        
        if (id) {
            // Обновление
            let found = false;
            for (let i = 0; i < this.movies.nowPlaying.length; i++) {
                if (this.movies.nowPlaying[i].id === parseInt(id)) {
                    movieData.sessions = this.movies.nowPlaying[i].sessions || [];
                    this.movies.nowPlaying[i] = movieData;
                    found = true;
                    break;
                }
            }
            if (!found) {
                for (let i = 0; i < this.movies.comingSoon.length; i++) {
                    if (this.movies.comingSoon[i].id === parseInt(id)) {
                        movieData.sessions = this.movies.comingSoon[i].sessions || [];
                        this.movies.comingSoon[i] = movieData;
                        break;
                    }
                }
            }
        } else {
            // Добавление
            this.movies.comingSoon.push(movieData);
        }
        
        this.saveMovies();
        this.closeModal('movie-modal');
        this.renderMovies();
        this.renderSessions();
        this.showMessage('Фильм сохранен!', 'success');
    },
    
    editMovie(id) {
        const allMovies = [...this.movies.nowPlaying, ...this.movies.comingSoon];
        const movie = allMovies.find(m => m.id === id);
        if (movie) this.openMovieModal(movie);
    },
    
    deleteMovie(id) {
        if (confirm('Удалить фильм? Все сеансы будут удалены.')) {
            this.movies.nowPlaying = this.movies.nowPlaying.filter(m => m.id !== id);
            this.movies.comingSoon = this.movies.comingSoon.filter(m => m.id !== id);
            this.saveMovies();
            this.renderMovies();
            this.renderSessions();
            this.showMessage('Фильм удален!', 'success');
        }
    },
    
    // ============ КИНОТЕАТРЫ ============
    renderCinemas() {
        const tbody = document.getElementById('cinemas-list');
        if (!tbody) return;
        
        if (this.cinemas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Нет кинотеатров</td></tr>';
            return;
        }
        
        tbody.innerHTML = this.cinemas.map(cinema => `
            <tr>
                <td>${cinema.id}</td>
                <td><strong>${cinema.name}</strong></td>
                <td>${cinema.address}</td>
                <td>${cinema.phone}</td>
                <td>${(cinema.features || []).slice(0, 2).join(', ')}${cinema.features && cinema.features.length > 2 ? '...' : ''}</td>
                <td>
                    <button class="edit-btn" onclick="adminApp.editCinema(${cinema.id})">✏️</button>
                    <button class="delete-btn" onclick="adminApp.deleteCinema(${cinema.id})">🗑️</button>
                </td>
            </tr>
        `).join('');
    },
    
    openCinemaModal(cinema = null) {
        const modal = document.getElementById('cinema-modal');
        modal.classList.add('active');
        
        if (cinema) {
            document.getElementById('cinema-modal-title').innerText = 'Редактировать кинотеатр';
            document.getElementById('cinema-id').value = cinema.id;
            document.getElementById('cinema-name').value = cinema.name || '';
            document.getElementById('cinema-address').value = cinema.address || '';
            document.getElementById('cinema-phone').value = cinema.phone || '';
            document.getElementById('cinema-email').value = cinema.email || '';
            document.getElementById('cinema-hours').value = cinema.workingHours || '';
            document.getElementById('cinema-description').value = cinema.description || '';
            document.getElementById('cinema-features').value = (cinema.features || []).join(', ');
        } else {
            document.getElementById('cinema-modal-title').innerText = 'Добавить кинотеатр';
            document.getElementById('cinema-id').value = '';
            document.getElementById('cinema-name').value = '';
            document.getElementById('cinema-address').value = '';
            document.getElementById('cinema-phone').value = '';
            document.getElementById('cinema-email').value = '';
            document.getElementById('cinema-hours').value = '';
            document.getElementById('cinema-description').value = '';
            document.getElementById('cinema-features').value = '';
        }
    },
    
    saveCinema() {
        const id = document.getElementById('cinema-id').value;
        const featuresStr = document.getElementById('cinema-features').value;
        const features = featuresStr ? featuresStr.split(',').map(f => f.trim()).filter(f => f) : [];
        
        const cinemaData = {
            id: id ? parseInt(id) : Date.now(),
            name: document.getElementById('cinema-name').value,
            nameEn: document.getElementById('cinema-name').value + ' En',
            address: document.getElementById('cinema-address').value,
            phone: document.getElementById('cinema-phone').value,
            email: document.getElementById('cinema-email').value,
            workingHours: document.getElementById('cinema-hours').value,
            description: document.getElementById('cinema-description').value,
            features: features
        };
        
        if (!cinemaData.name) { this.showMessage('Введите название!', 'error'); return; }
        
        if (id) {
            const index = this.cinemas.findIndex(c => c.id === parseInt(id));
            if (index !== -1) this.cinemas[index] = cinemaData;
        } else {
            this.cinemas.push(cinemaData);
        }
        
        this.saveCinemas();
        this.closeModal('cinema-modal');
        this.renderCinemas();
        this.loadHallCinemaSelect();
        this.loadCinemaSelects();
        this.showMessage('Кинотеатр сохранен!', 'success');
    },
    
    editCinema(id) {
        const cinema = this.cinemas.find(c => c.id === id);
        if (cinema) this.openCinemaModal(cinema);
    },
    
    deleteCinema(id) {
        if (confirm('Удалить кинотеатр?')) {
            this.cinemas = this.cinemas.filter(c => c.id !== id);
            this.saveCinemas();
            this.renderCinemas();
            this.loadHallCinemaSelect();
            this.loadCinemaSelects();
            this.showMessage('Кинотеатр удален!', 'success');
        }
    },
    
    // ============ ЗАЛЫ ============
    loadHallCinemaSelect() {
        const select = document.getElementById('hall-cinema-select');
        if (!select) return;
        select.innerHTML = '<option value="">Выберите кинотеатр</option>' + 
            this.cinemas.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    },
    
    loadHallsList() {
        const cinemaId = document.getElementById('hall-cinema-select').value;
        const container = document.getElementById('halls-list-container');
        
        if (!cinemaId) {
            container.innerHTML = '<div class="info-message">Выберите кинотеатр</div>';
            return;
        }
        
        const cinemaHalls = {};
        for (const [hallId, hall] of Object.entries(this.halls)) {
            if (hall.cinemaId == cinemaId) cinemaHalls[hallId] = hall;
        }
        
        if (Object.keys(cinemaHalls).length === 0) {
            container.innerHTML = '<div class="info-message">Нет залов. Добавьте первый!</div>';
            return;
        }
        
        container.innerHTML = `
            <div class="halls-grid">
                ${Object.entries(cinemaHalls).map(([hallId, hall]) => `
                    <div class="hall-card">
                        <div class="hall-card-header">
                            <h4>${hall.name}</h4>
                            <div>
                                <button class="edit-btn" onclick="adminApp.openHallModal('${hallId}', ${cinemaId})">✏️</button>
                                <button class="delete-btn" onclick="adminApp.deleteHall('${hallId}', ${cinemaId})">🗑️</button>
                            </div>
                        </div>
                        <div class="hall-card-body">
                            <div>📏 Рядов: ${hall.rows}</div>
                            <div>💺 Мест: ${hall.seatsPerRow}</div>
                            <div>⭐ VIP: ${hall.vipSeats ? hall.vipSeats.length : 0}</div>
                            <div>♿ Инвалиды: ${hall.disabledSeats ? hall.disabledSeats.length : 0}</div>
                        </div>
                        <div class="hall-card-footer">
                            <button class="view-btn" onclick="adminApp.previewHall('${hallId}')">Посмотреть</button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    },
    
    openHallModal(hallId = null, cinemaId = null) {
        const modal = document.getElementById('hall-modal');
        modal.classList.add('active');
        
        if (hallId && this.halls[hallId]) {
            document.getElementById('hall-id').value = hallId;
            document.getElementById('hall-cinema-id').value = cinemaId;
            document.getElementById('hall-name').value = this.halls[hallId].name || '';
            document.getElementById('hall-rows').value = this.halls[hallId].rows || 8;
            document.getElementById('hall-seats-per-row').value = this.halls[hallId].seatsPerRow || 12;
            document.getElementById('hall-vip-seats').value = (this.halls[hallId].vipSeats || []).join(', ');
            document.getElementById('hall-disabled-seats').value = (this.halls[hallId].disabledSeats || []).join(', ');
        } else {
            document.getElementById('hall-id').value = '';
            document.getElementById('hall-cinema-id').value = document.getElementById('hall-cinema-select').value;
            document.getElementById('hall-name').value = '';
            document.getElementById('hall-rows').value = 8;
            document.getElementById('hall-seats-per-row').value = 12;
            document.getElementById('hall-vip-seats').value = '';
            document.getElementById('hall-disabled-seats').value = '';
        }
    },
    
    saveHall() {
        const cinemaId = parseInt(document.getElementById('hall-cinema-id').value);
        const hallId = document.getElementById('hall-id').value;
        const hallName = document.getElementById('hall-name').value;
        
        if (!cinemaId) { this.showMessage('Выберите кинотеатр!', 'error'); return; }
        if (!hallName) { this.showMessage('Введите название зала!', 'error'); return; }
        
        const vipSeats = document.getElementById('hall-vip-seats').value.split(',').map(s => s.trim()).filter(s => s);
        const disabledSeats = document.getElementById('hall-disabled-seats').value.split(',').map(s => s.trim()).filter(s => s);
        const newHallId = hallId || `hall_${cinemaId}_${Date.now()}`;
        
        this.halls[newHallId] = {
            cinemaId: cinemaId,
            name: hallName,
            rows: parseInt(document.getElementById('hall-rows').value),
            seatsPerRow: parseInt(document.getElementById('hall-seats-per-row').value),
            vipSeats: vipSeats,
            disabledSeats: disabledSeats
        };
        
        this.saveHalls();
        this.closeModal('hall-modal');
        this.loadHallsList();
        this.showMessage('Зал сохранен!', 'success');
    },
    
    deleteHall(hallId, cinemaId) {
        if (confirm('Удалить зал?')) {
            delete this.halls[hallId];
            this.saveHalls();
            this.loadHallsList();
            this.showMessage('Зал удален!', 'success');
        }
    },
    
    previewHall(hallId) {
        const hall = this.halls[hallId];
        if (!hall) return;
        
        let html = '<div style="padding: 20px; text-align: center;"><h4>' + hall.name + '</h4>';
        html += '<div style="margin: 20px;"><div style="width: 80%; height: 3px; background: #3A4DB3; margin: 0 auto;"></div><div style="font-size: 12px;">ЭКРАН</div></div>';
        
        for (let row = 1; row <= hall.rows; row++) {
            html += '<div style="display: flex; justify-content: center; gap: 4px; margin: 4px;">';
            for (let seat = 1; seat <= hall.seatsPerRow; seat++) {
                const seatId = row + '-' + seat;
                let style = 'width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #e8eaf6; border-radius: 6px; font-size: 10px;';
                if (hall.vipSeats && hall.vipSeats.includes(seatId)) style = 'background: linear-gradient(135deg, #ffd700, #ffed4e);';
                if (hall.disabledSeats && hall.disabledSeats.includes(seatId)) style = 'background: #2196f3; color: white;';
                html += `<div style="${style}">${seat}</div>`;
            }
            html += '</div>';
        }
        html += '</div>';
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:3000;';
        modal.innerHTML = `<div style="background:white; border-radius:16px; padding:20px; max-width:90%; max-height:80%; overflow:auto;">${html}<div style="text-align:center; margin-top:20px;"><button onclick="this.closest('div').parentElement.remove()" style="padding:8px 20px; background:#3A4DB3; color:white; border:none; border-radius:8px;">Закрыть</button></div></div>`;
        document.body.appendChild(modal);
    },
    
    // ============ СЕАНСЫ ============
    loadCinemaSelects() {
        const cinemaSelect = document.getElementById('session-cinema');
        if (cinemaSelect) {
            cinemaSelect.innerHTML = '<option value="">Выберите кинотеатр</option>' + 
                this.cinemas.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        }
        
        const movieSelect = document.getElementById('session-movie');
        if (movieSelect) {
            movieSelect.innerHTML = '<option value="">Выберите фильм</option>' + 
                this.movies.nowPlaying.map(m => `<option value="${m.id}">${m.title}</option>`).join('');
        }
    },
    
    updateHallOptions() {
        const cinemaId = document.getElementById('session-cinema').value;
        const hallSelect = document.getElementById('session-hall');
        
        if (!cinemaId) {
            hallSelect.innerHTML = '<option value="">Сначала выберите кинотеатр</option>';
            return;
        }
        
        const halls = [];
        for (const [id, hall] of Object.entries(this.halls)) {
            if (hall.cinemaId == cinemaId) halls.push({ id, name: hall.name });
        }
        
        if (halls.length === 0) {
            hallSelect.innerHTML = '<option value="">Нет залов</option>';
        } else {
            hallSelect.innerHTML = '<option value="">Выберите зал</option>' + 
                halls.map(h => `<option value="${h.id}">${h.name}</option>`).join('');
        }
    },
    
    renderSessions() {
        const tbody = document.getElementById('sessions-list');
        if (!tbody) return;
        
        let allSessions = [];
        this.movies.nowPlaying.forEach(movie => {
            if (movie.sessions && movie.sessions.length > 0) {
                movie.sessions.forEach(session => {
                    const cinema = this.cinemas.find(c => c.id === session.cinemaId);
                    const hall = this.halls[session.hallId];
                    allSessions.push({
                        ...session,
                        movieTitle: movie.title,
                        movieId: movie.id,
                        cinemaName: cinema ? cinema.name : 'Неизвестно',
                        hallName: hall ? hall.name : session.hall
                    });
                });
            }
        });
        
        if (allSessions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Нет сеансов</td></tr>';
            return;
        }
        
        tbody.innerHTML = allSessions.map(session => `
            <tr>
                <td><strong>${session.movieTitle}</strong></td>
                <td>${session.cinemaName}</td>
                <td>${session.hallName}</td>
                <td>${session.time}</td>
                <td>${session.price} ₽</td>
                <td><button class="delete-btn" onclick="adminApp.deleteSession(${session.movieId}, '${session.time}')">🗑️</button></td>
            </tr>
        `).join('');
    },
    
    openSessionModal() {
        const modal = document.getElementById('session-modal');
        modal.classList.add('active');
        this.loadCinemaSelects();
        document.getElementById('session-time').value = '';
        document.getElementById('session-price').value = '';
    },
    
    saveSession() {
        const movieId = parseInt(document.getElementById('session-movie').value);
        const cinemaId = parseInt(document.getElementById('session-cinema').value);
        const hallId = document.getElementById('session-hall').value;
        const datetime = document.getElementById('session-time').value;
        const price = parseInt(document.getElementById('session-price').value);
        
        if (!movieId || !cinemaId || !hallId || !datetime || !price) {
            this.showMessage('Заполните все поля!', 'error');
            return;
        }
        
        const cinema = this.cinemas.find(c => c.id === cinemaId);
        const hall = this.halls[hallId];
        const movie = this.movies.nowPlaying.find(m => m.id === movieId);
        
        if (movie && cinema && hall) {
            if (!movie.sessions) movie.sessions = [];
            
            const exists = movie.sessions.some(s => s.cinemaId === cinemaId && s.hallId === hallId && s.time === datetime);
            if (exists) {
                this.showMessage('Такой сеанс уже есть!', 'error');
                return;
            }
            
            movie.sessions.push({
                cinemaId: cinemaId,
                cinema: cinema.name,
                time: datetime,
                hallId: hallId,
                hall: hall.name,
                price: price
            });
            
            this.saveMovies();
            this.closeModal('session-modal');
            this.renderSessions();
            this.showMessage('Сеанс добавлен!', 'success');
        }
    },
    
    deleteSession(movieId, time) {
        if (confirm('Удалить сеанс?')) {
            const movie = this.movies.nowPlaying.find(m => m.id === movieId);
            if (movie && movie.sessions) {
                movie.sessions = movie.sessions.filter(s => s.time !== time);
                this.saveMovies();
                this.renderSessions();
                this.showMessage('Сеанс удален!', 'success');
            }
        }
    },
    
    // ============ БРОНИРОВАНИЯ ============
    getBookings() {
        const bookings = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith('booked_')) {
                try {
                    const data = JSON.parse(localStorage.getItem(key));
                    if (data && data.seats) {
                        const parts = key.split('_');
                        const movieId = parseInt(parts[4]);
                        let movieTitle = 'Неизвестно';
                        for (let m of [...this.movies.nowPlaying, ...this.movies.comingSoon]) {
                            if (m.id === movieId) { movieTitle = m.title; break; }
                        }
                        const cinema = this.cinemas.find(c => c.id == parts[1]);
                        const hall = this.halls[parts[2]];
                        bookings.push({
                            movieTitle: movieTitle,
                            cinemaName: cinema ? cinema.name : 'Неизвестно',
                            hallName: hall ? hall.name : parts[2],
                            time: parts[3],
                            seats: data.seats,
                            status: data.expiry > Date.now() ? 'активно' : 'истекло',
                            key: key
                        });
                    }
                } catch(e) {}
            }
        }
        return bookings;
    },
    
    renderBookings() {
        const tbody = document.getElementById('bookings-list');
        const statsContainer = document.getElementById('bookings-stats');
        const bookings = this.getBookings();
        
        if (bookings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Нет бронирований</td></tr>';
            if (statsContainer) statsContainer.innerHTML = '<div class="info-message">Нет данных</div>';
            return;
        }
        
        tbody.innerHTML = bookings.map(booking => `
            <tr>
                <td>${booking.movieTitle}</td>
                <td>${booking.cinemaName}</td>
                <td>${booking.hallName}</td>
                <td>${booking.time}</td>
                <td>${booking.seats.join(', ')}</td>
                <td><span class="status-badge ${booking.status === 'активно' ? 'active' : 'expired'}">${booking.status}</span></td>
                <td><button class="delete-btn" onclick="adminApp.cancelBooking('${booking.key}')">Отменить</button></td>
            </tr>
        `).join('');
        
        if (statsContainer) {
            const activeCount = bookings.filter(b => b.status === 'активно').length;
            statsContainer.innerHTML = `
                <div class="stat-card"><h4>Всего</h4><div class="stat-value">${bookings.length}</div></div>
                <div class="stat-card"><h4>Активных</h4><div class="stat-value">${activeCount}</div></div>
            `;
        }
    },
    
    cancelBooking(key) {
        if (confirm('Отменить бронирование?')) {
            localStorage.removeItem(key);
            this.renderBookings();
            this.showMessage('Бронирование отменено!', 'success');
        }
    },
    
    // ============ ВСПОМОГАТЕЛЬНЫЕ ============
    formatDateForInput(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('.');
        if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
        return '';
    },
    
    formatDateForSave(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length === 3) return `${parts[2]}.${parts[1]}.${parts[0]}`;
        return '';
    },
    
    closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    },
    
    showMessage(message, type) {
        const msg = document.createElement('div');
        msg.className = 'toast-message';
        msg.textContent = message;
        msg.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 3000);
    },
    
    exportData() {
        const data = {
            movies: this.movies,
            cinemas: this.cinemas,
            halls: this.halls,
            exportDate: new Date().toISOString()
        };
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `slivkino_backup_${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        URL.revokeObjectURL(url);
        this.showMessage('Экспорт выполнен!', 'success');
    },
    
    importData() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'application/json';
        input.onchange = (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const data = JSON.parse(event.target.result);
                    if (data.movies) { this.movies = data.movies; this.saveMovies(); }
                    if (data.cinemas) { this.cinemas = data.cinemas; this.saveCinemas(); }
                    if (data.halls) { this.halls = data.halls; this.saveHalls(); }
                    this.showMessage('Импорт выполнен! Перезагрузка...', 'success');
                    setTimeout(() => location.reload(), 1500);
                } catch(err) { alert('Ошибка: ' + err.message); }
            };
            reader.readAsText(file);
        };
        input.click();
    },
    
    resetData() {
        if (confirm('Сбросить все данные? Это удалит все изменения!')) {
            localStorage.removeItem('slivkino_movies');
            localStorage.removeItem('slivkino_cinemas');
            localStorage.removeItem('slivkino_halls');
            location.reload();
        }
    }
};

// Глобальный доступ
window.adminApp = AdminApp;

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    AdminApp.init();
});