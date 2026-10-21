// Глобальная функция для перехода к выбору мест
window.selectSession = function (movieId, cinemaId, hallId, time, price, hallName) {
    console.log('Переход к выбору мест:', { movieId, cinemaId, hallId, time, price, hallName });
    window.location.href = 'hall.html?movieId=' + movieId + '&cinemaId=' + cinemaId + '&hallId=' + hallId + '&time=' + encodeURIComponent(time) + '&price=' + price + '&hallName=' + encodeURIComponent(hallName);
};

function getMovieIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return parseInt(params.get('id'));
}

function displaySessions(sessions, movieId) {
    if (!sessions || sessions.length === 0) {
        return `<div class="no-sessions"><p>Сеансы появятся ближе к дате премьеры!</p></div>`;
    }

    const groupedByCinema = {};
    sessions.forEach(session => {
        if (!groupedByCinema[session.cinema]) {
            groupedByCinema[session.cinema] = [];
        }
        groupedByCinema[session.cinema].push(session);
    });

    let sessionsHtml = '<div class="sessions-container">';
    for (const [cinema, cinemaSessions] of Object.entries(groupedByCinema)) {
        sessionsHtml += `
            <div class="cinema-sessions">
                <h3 class="cinema-name">${cinema}</h3>
                <div class="sessions-grid">
        `;
        cinemaSessions.forEach(session => {
            sessionsHtml += `
                <div class="session-card" onclick="window.selectSession(${movieId}, ${session.cinemaId}, '${session.hallId}', '${session.time}', ${session.price}, '${session.hall}')">
                    <div class="session-time">${session.time}</div>
                    <div class="session-hall">${session.hall}</div>
                    <div class="session-price">${session.price}₽</div>
                    <button class="session-buy-btn">Выбрать места</button>
                </div>
            `;
        });
        sessionsHtml += `</div></div>`;
    }
    sessionsHtml += '</div>';
    return sessionsHtml;
}

function displayMovieDetail(movie) {
    const container = document.getElementById('movie-detail');

    if (!movie) {
        container.innerHTML = `
            <div class="not-found">
                <h2>Фильм не найден</h2>
                <p>К сожалению, такого фильма нет в нашей базе.</p>
                <a href="index.html" class="back-link">← Назад к афише</a>
            </div>
        `;
        return;
    }

    const genreTags = movie.genres.map(genre => `<span class="detail-genre-tag">${genre}</span>`).join('');
    const castHtml = movie.cast ? `<div class="movie-detail-cast"><h3>В главных ролях</h3><div class="cast-list">${movie.cast.map(actor => `<span class="cast-item">${actor}</span>`).join('')}</div></div>` : '';
    const additionalInfo = `<div class="movie-detail-additional">${movie.director ? `<div class="info-row"><span class="info-label">Режиссер:</span> <span class="info-value">${movie.director}</span></div>` : ''}${movie.duration ? `<div class="info-row"><span class="info-label">Длительность:</span> <span class="info-value">${movie.duration}</span></div>` : ''}</div>`;
    const posterHtml = movie.poster ? `<img src="${movie.poster}" alt="${movie.title}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'poster-placeholder\\'>Нет постера</div>';">` : `<div class="poster-placeholder">🎬</div>`;
    const sessionsHtml = `<div class="movie-detail-sessions"><h3>Расписание сеансов</h3>${displaySessions(movie.sessions, movie.id)}</div>`;

    container.innerHTML = `
        <a href="index.html" class="back-link">← Назад к афише</a>
        <div class="movie-detail">
            <div class="movie-detail-poster">${posterHtml}</div>
            <div class="movie-detail-info">
                <h1 class="movie-detail-title">${movie.title}</h1>
                ${movie.titleEn ? `<div class="movie-detail-title-en">${movie.titleEn}</div>` : ''}
                <div class="movie-detail-meta">
                    <div class="meta-item"><span class="meta-label">Страна:</span><span class="meta-value">${movie.country}</span></div>
                    <div class="meta-item"><span class="meta-label">Год:</span><span class="meta-value">${movie.year}</span></div>
                    <div class="meta-item"><span class="meta-label">Возраст:</span><span class="meta-value">${movie.ageRating}</span></div>
                </div>
                <div class="movie-detail-genres">${genreTags}</div>
                ${additionalInfo}
                <div class="movie-detail-description"><h3>О фильме</h3><p>${movie.fullDescription || movie.description}</p></div>
                ${castHtml}
                <div class="movie-detail-dates">
                    <div class="date-item"><strong>Дата выхода:</strong> ${movie.releaseDate}</div>
                    <div class="date-item"><strong>Окончание проката:</strong> ${movie.endDate}</div>
                </div>
            </div>
        </div>
        ${sessionsHtml}
    `;
    document.title = `Сливкино - ${movie.title}`;
}

document.addEventListener('DOMContentLoaded', () => {
    const movieId = getMovieIdFromURL();
    console.log('ID фильма из URL:', movieId);
    const movie = getMovieById(movieId);
    console.log('Найденный фильм:', movie);
    displayMovieDetail(movie);
});