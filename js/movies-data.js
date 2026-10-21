// База данных фильмов
var moviesDatabase = {
    nowPlaying: [
        {
            id: 1,
            title: "Горничная",
            titleEn: "The Housemaid",
            genres: ["триллер", "детектив", "драма"],
            country: "США",
            year: 2026,
            ageRating: "18+",
            releaseDate: "08.01.2026",
            endDate: "20.03.2026",
            poster: "images/posters/maid.jpg",
            description: "Психологический триллер с Сидни Суини. В российском прокате с 8 января.",
            fullDescription: "Молодая женщина устраивается горничной в богатый дом, но вскоре обнаруживает, что идеальная семья скрывает мрачные тайны.",
            director: "Пол Верховен",
            cast: ["Сидни Суини", "Аманда Сейфрид", "Мишель Пфайффер"],
            duration: "118 минут",
            sessions: [
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "10:00", hallId: "hall1", hall: "Зал 1", price: 350 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "13:30", hallId: "hall1", hall: "Зал 1", price: 400 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "17:00", hallId: "hall2", hall: "Зал 2", price: 450 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "20:30", hallId: "hall1", hall: "Зал 1", price: 500 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "11:00", hallId: "hall3", hall: "Зал 3", price: 350 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "14:30", hallId: "hall3", hall: "Зал 3", price: 400 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "18:00", hallId: "hall4", hall: "Зал 4", price: 450 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "21:00", hallId: "hall3", hall: "Зал 3", price: 500 }
            ]
        },
        {
            id: 2,
            title: "Прыгуны",
            titleEn: "Jumpers",
            genres: ["приключения", "мультфильм", "семейный", "комедия", "фантастика"],
            country: "США",
            year: 2026,
            ageRating: "6+",
            releaseDate: "15.01.2026",
            endDate: "25.03.2026",
            poster: "images/posters/foodland.jpg",
            description: "Яркий анимационный фильм о приключениях продуктов в супермаркете.",
            fullDescription: "Когда супермаркет закрывается, продукты оживают! Яблоко по имени Джим и его друзья отправляются в опасное путешествие.",
            director: "Фил Лорд",
            cast: ["Крис Пратт", "Анна Кендрик", "Сэмюэл Л. Джексон"],
            duration: "95 минут",
            sessions: [
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "09:30", hallId: "hall5", hall: "Зал 5", price: 250 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "12:00", hallId: "hall5", hall: "Зал 5", price: 300 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "15:30", hallId: "hall5", hall: "Зал 5", price: 350 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "10:30", hallId: "hall2", hall: "Зал 2", price: 250 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "13:00", hallId: "hall2", hall: "Зал 2", price: 300 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "16:30", hallId: "hall2", hall: "Зал 2", price: 350 }
            ]
        },
        {
            id: 3,
            title: "Чебурашка 2",
            titleEn: "Cheburashka 2",
            genres: ["семейный", "комедия"],
            country: "Россия",
            year: 2026,
            ageRating: "6+",
            releaseDate: "01.01.2026",
            endDate: "30.03.2026",
            poster: "images/posters/cheburashka2.jpg",
            description: "Продолжение самой кассовой российской комедии.",
            fullDescription: "Чебурашка и Гена возвращаются в новых приключениях! На этот раз им предстоит спасти свой любимый апельсиновый сад от застройки.",
            director: "Дмитрий Дьяченко",
            cast: ["Сергей Гармаш", "Ольга Кузьмина", "Полина Максимова"],
            duration: "110 минут",
            sessions: [
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "10:00", hallId: "hall3", hall: "Зал 3", price: 300 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "13:00", hallId: "hall3", hall: "Зал 3", price: 350 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "16:00", hallId: "hall4", hall: "Зал 4", price: 400 },
                { cinemaId: 1, cinema: "Сливкино Плаза", time: "19:00", hallId: "hall3", hall: "Зал 3", price: 450 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "11:00", hallId: "hall1", hall: "Зал 1", price: 300 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "14:00", hallId: "hall1", hall: "Зал 1", price: 350 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "17:00", hallId: "hall1", hall: "Зал 1", price: 400 },
                { cinemaId: 2, cinema: "Сливкино Молл", time: "20:00", hallId: "hall1", hall: "Зал 1", price: 450 }
            ]
        }
    ],
    comingSoon: [
        {
            id: 4,
            title: "Марти Великолепный",
            titleEn: "Marty Magnificent",
            genres: ["драма", "комедия"],
            country: "США",
            year: 2026,
            ageRating: "16+",
            releaseDate: "05.04.2026",
            endDate: "10.05.2026",
            poster: "",
            description: "Новый фильм с участием популярных голливудских актеров.",
            fullDescription: "История о неудачнике Марти, который внезапно получает суперспособности. Сможет ли он использовать их во благо?",
            director: "Тайка Вайтити",
            cast: ["Джош Бролин", "Флоренс Пью", "Педро Паскаль"],
            duration: "125 минут",
            sessions: []
        },
        {
            id: 5,
            title: "Аватар 3",
            titleEn: "Avatar 3",
            genres: ["фантастика", "приключения"],
            country: "США",
            year: 2026,
            ageRating: "12+",
            releaseDate: "19.04.2026",
            endDate: "15.06.2026",
            poster: "",
            description: "Третья часть эпической саги Джеймса Кэмерона.",
            fullDescription: "Продолжение приключений Джейка Салли на Пандоре. Новые племена, новые опасности и эпические битвы.",
            director: "Джеймс Кэмерон",
            cast: ["Сэм Уортингтон", "Зои Салдана", "Сигурни Уивер"],
            duration: "190 минут",
            sessions: []
        }
    ]
};

// Конфигурация залов с местами
var hallsConfig = {
    hall1: {
        name: "Зал 1",
        rows: 8,
        seatsPerRow: 12,
        vipSeats: ["3-4", "3-5", "3-6", "3-7", "4-4", "4-5", "4-6", "4-7"],
        disabledSeats: ["1-1", "1-12", "8-1", "8-12"]
    },
    hall2: {
        name: "Зал 2",
        rows: 7,
        seatsPerRow: 10,
        vipSeats: ["2-4", "2-5", "2-6", "2-7", "3-4", "3-5", "3-6", "3-7"],
        disabledSeats: ["1-1", "1-10", "7-1", "7-10"]
    },
    hall3: {
        name: "Зал 3",
        rows: 6,
        seatsPerRow: 8,
        vipSeats: ["2-3", "2-4", "2-5", "2-6", "3-3", "3-4", "3-5", "3-6"],
        disabledSeats: ["1-1", "1-8", "6-1", "6-8"]
    },
    hall4: {
        name: "Зал 4",
        rows: 10,
        seatsPerRow: 14,
        vipSeats: ["4-6", "4-7", "4-8", "4-9", "5-6", "5-7", "5-8", "5-9"],
        disabledSeats: ["1-1", "1-14", "10-1", "10-14"]
    },
    hall5: {
        name: "Зал 5",
        rows: 6,
        seatsPerRow: 8,
        vipSeats: ["2-3", "2-4", "2-5", "2-6", "3-3", "3-4", "3-5", "3-6"],
        disabledSeats: ["1-1", "1-8", "6-1", "6-8"]
    }
};

// Функции для работы с данными
function getMovieById(id) {
    var allMovies = moviesDatabase.nowPlaying.concat(moviesDatabase.comingSoon);
    for (var i = 0; i < allMovies.length; i++) {
        if (allMovies[i].id === id) {
            return allMovies[i];
        }
    }
    return null;
}

function getSessionByParams(movieId, cinemaId, hallId, time) {
    var movie = getMovieById(movieId);
    if (!movie || !movie.sessions) return null;
    for (var i = 0; i < movie.sessions.length; i++) {
        var s = movie.sessions[i];
        if (s.cinemaId === cinemaId && s.hallId === hallId && s.time === time) {
            return s;
        }
    }
    return null;
}

console.log('movies-data.js загружен');
console.log('Фильмов в прокате:', moviesDatabase.nowPlaying.length);
console.log('Фильмов скоро:', moviesDatabase.comingSoon.length);
// Синхронизация с серверной БД: при запуске через npm start серверные данные главнее демо-данных.
fetch('/api/state').then(r => r.ok ? r.json() : null).then(state => {
    if (state && state.movies) {
        moviesDatabase.nowPlaying = state.movies.nowPlaying || [];
        moviesDatabase.comingSoon = state.movies.comingSoon || [];
        if (state.halls) Object.keys(state.halls).forEach(k => { hallsConfig[k] = state.halls[k]; });
        window.dispatchEvent(new CustomEvent('slivkino:data-loaded'));
    }
}).catch(() => {});
