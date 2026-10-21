function getCinemaIdFromURL() {
    var params = new URLSearchParams(window.location.search);
    return parseInt(params.get('id'));
}

function displayCinemaDetail(cinema) {
    var container = document.getElementById('cinema-detail');

    if (!cinema) {
        container.innerHTML = '<div class="not-found"><h2>Кинотеатр не найден</h2><a href="cinemas.html" class="back-link">Назад к списку</a></div>';
        return;
    }

    var movies = getMoviesByCinema(cinema.name);

    var moviesHtml = '';
    if (movies.length > 0) {
        moviesHtml = '<div class="movies-section"><h3>Сейчас в прокате</h3><div class="movies-list">';

        for (var i = 0; i < movies.length; i++) {
            var movie = movies[i];
            var sessionTimes = '';
            for (var j = 0; j < movie.cinemaSessions.length; j++) {
                sessionTimes += '<span class="session-time-badge">' + movie.cinemaSessions[j].time + '</span>';
            }

            var genreTags = '';
            for (var g = 0; g < movie.genres.length; g++) {
                genreTags += '<span class="movie-item-genre">' + movie.genres[g] + '</span>';
            }

            moviesHtml += '<a href="movie.html?id=' + movie.id + '" class="movie-item">' +
                '<div class="movie-item-info">' +
                '<div class="movie-item-title">' + movie.title + '</div>' +
                '<div class="movie-item-genres">' + genreTags + '</div>' +
                '</div>' +
                '<div class="movie-item-sessions">' +
                '<div class="session-times">' + sessionTimes + '</div>' +
                '</div>' +
                '</a>';
        }

        moviesHtml += '</div></div>';
    } else {
        moviesHtml = '<div class="movies-section"><h3>Сейчас в прокате</h3><div class="no-movies"><p>В этом кинотеатре пока нет сеансов</p></div></div>';
    }

    var featuresHtml = '';
    for (var f = 0; f < cinema.features.length; f++) {
        featuresHtml += '<span class="feature-tag">' + cinema.features[f] + '</span>';
    }

    container.innerHTML = '<a href="cinemas.html" class="back-link">Назад к списку кинотеатров</a>' +
        '<div class="cinema-detail-card">' +
        '<div class="cinema-detail-header">' +
        '<h1 class="cinema-detail-name">' + cinema.name + '</h1>' +
        '<div class="cinema-detail-address">' + cinema.address + '</div>' +
        '</div>' +
        '<div class="cinema-detail-content">' +
        '<div class="info-grid">' +
        '<div class="info-card">' +
        '<h3>Контакты</h3>' +
        '<p class="phone">' + cinema.phone + '</p>' +
        '<p>' + cinema.email + '</p>' +
        '</div>' +
        '<div class="info-card">' +
        '<h3>Режим работы</h3>' +
        '<p>' + cinema.workingHours + '</p>' +
        '</div>' +
        '<div class="info-card">' +
        '<h3>Оснащение</h3>' +
        '<div class="features-list">' + featuresHtml + '</div>' +
        '</div>' +
        '</div>' +
        '<div class="description-section">' +
        '<h3>О кинотеатре</h3>' +
        '<p>' + cinema.description + '</p>' +
        '</div>' +
        moviesHtml +
        '</div>' +
        '</div>';

    document.title = 'Сливкино - ' + cinema.name;
}

document.addEventListener('DOMContentLoaded', function () {
    var cinemaId = getCinemaIdFromURL();
    var cinema = getCinemaById(cinemaId);
    displayCinemaDetail(cinema);
});