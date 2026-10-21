function displayCinemasList() {
    const container = document.getElementById('cinemas-list');
    if (!container) return;

    if (typeof cinemasDatabase === 'undefined' || cinemasDatabase.length === 0) {
        container.innerHTML = '<div class="no-movies">Кинотеатры не найдены</div>';
        return;
    }

    container.innerHTML = cinemasDatabase.map(cinema => {
        const featuresHtml = cinema.features.slice(0, 3).map(feature =>
            `<span class="feature-tag">${feature}</span>`
        ).join('');

        return `
            <a href="cinema-detail.html?id=${cinema.id}" class="cinema-card">
                <div class="cinema-preview">
                    <div class="cinema-preview-placeholder">🎬</div>
                </div>
                <div class="cinema-info">
                    <h3 class="cinema-name">${cinema.name}</h3>
                    <div class="cinema-address">${cinema.address}</div>
                    <div class="cinema-features">
                        ${featuresHtml}
                        ${cinema.features.length > 3 ? `<span class="feature-tag">+${cinema.features.length - 3}</span>` : ''}
                    </div>
                    <div class="cinema-hours">${cinema.workingHours}</div>
                </div>
            </a>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', () => {
    displayCinemasList();
});