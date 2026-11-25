var cinemasDatabase = [
    {
        id: 1,
        name: "Сливкино Плаза",
        nameEn: "Slivkino Plaza",
        address: "г. Москва, ул. Тверская, 15",
        phone: "+7 (495) 123-45-67",
        email: "plaza@slivkino.ru",
        workingHours: "Ежедневно с 09:00 до 02:00",
        description: "Современный мультиплекс в самом сердце Москвы. 7 залов, включая IMAX и VIP-зал. Комфортные кресла, современное оборудование, кафе и зона отдыха. Идеальное место для семейного просмотра и свиданий.",
        features: ["IMAX", "VIP зал", "3D", "Кафе", "Парковка", "Звук Dolby Atmos"]
    },
    {
        id: 2,
        name: "Сливкино Молл",
        nameEn: "Slivkino Mall",
        address: "г. Москва, пр-т Вернадского, 6",
        phone: "+7 (495) 234-56-78",
        email: "mall@slivkino.ru",
        workingHours: "Ежедневно с 10:00 до 01:00",
        description: "Уютный кинотеатр в крупном торговом центре. 5 залов с удобными креслами-реклайнерами. Отличное место, чтобы совместить шопинг и просмотр нового фильма. Детские утренники по выходным.",
        features: ["Кресла-реклайнеры", "3D", "Детские сеансы", "Парковка", "Кафе"]
    },
    {
        id: 3,
        name: "Сливкино Океан",
        nameEn: "Slivkino Ocean",
        address: "г. Санкт-Петербург, Невский пр-т, 88",
        phone: "+7 (812) 345-67-89",
        email: "ocean@slivkino.ru",
        workingHours: "Ежедневно с 09:30 до 02:30",
        description: "Культовый кинотеатр на главном проспекте Петербурга. 6 залов с историческим интерьером и современным оснащением. Особенность - зал с панорамным экраном и акустикой высшего класса.",
        features: ["Панорамный экран", "VIP зал", "Бар", "Исторический интерьер", "Звук Dolby"]
    }
];

function getCinemaById(id) {
    for (var i = 0; i < cinemasDatabase.length; i++) {
        if (cinemasDatabase[i].id === id) {
            return cinemasDatabase[i];
        }
    }
    return null;
}

function getMoviesByCinema(cinemaName) {
    var allMovies = [];
    if (typeof moviesDatabase !== 'undefined') {
        allMovies = moviesDatabase.nowPlaying.concat(moviesDatabase.comingSoon);
    }

    var moviesWithSessions = [];

    for (var i = 0; i < allMovies.length; i++) {
        var movie = allMovies[i];
        if (movie.sessions && movie.sessions.length > 0) {
            var cinemaSessions = [];
            for (var j = 0; j < movie.sessions.length; j++) {
                if (movie.sessions[j].cinema === cinemaName) {
                    cinemaSessions.push(movie.sessions[j]);
                }
            }
            if (cinemaSessions.length > 0) {
                var movieCopy = {
                    id: movie.id,
                    title: movie.title,
                    genres: movie.genres,
                    sessions: movie.sessions,
                    cinemaSessions: cinemaSessions
                };
                moviesWithSessions.push(movieCopy);
            }
        }
    }

    return moviesWithSessions;
}
// Загружаем обновлённые кинотеатры из общей БД.
fetch('/api/state').then(r => r.ok ? r.json() : null).then(state => {
    if (state && Array.isArray(state.cinemas)) {
        cinemasDatabase.length = 0;
        cinemasDatabase.push(...state.cinemas);
        window.dispatchEvent(new CustomEvent('slivkino:data-loaded'));
    }
}).catch(() => {});
