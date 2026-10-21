// Получение параметров из URL
function getParamsFromURL() {
    var params = new URLSearchParams(window.location.search);
    return {
        movieId: parseInt(params.get('movieId')),
        cinemaId: parseInt(params.get('cinemaId')),
        hallId: params.get('hallId'),
        time: params.get('time'),
        price: parseInt(params.get('price')),
        hallName: params.get('hallName')
    };
}

var selectedSeats = [];
var timerInterval = null;
var timeLeft = 60;
var isPaymentModalOpen = false;

// Получение ключа для хранения забронированных мест
function getBookedSeatsKey() {
    var params = getParamsFromURL();
    return 'booked_' + params.cinemaId + '_' + params.hallId + '_' + params.time + '_' + params.movieId;
}

// Загрузка забронированных мест
function loadBookedSeats() {
    var key = getBookedSeatsKey();
    var saved = localStorage.getItem(key);
    if (saved) {
        try {
            var parsed = JSON.parse(saved);
            var now = Date.now();
            if (parsed.expiry > now) {
                return parsed.seats;
            } else {
                localStorage.removeItem(key);
            }
        } catch (e) {
            localStorage.removeItem(key);
        }
    }
    return [];
}

// Сохранение забронированных мест
function saveBookedSeats(seats, expiryTimestamp) {
    var key = getBookedSeatsKey();
    localStorage.setItem(key, JSON.stringify({
        seats: seats,
        expiry: expiryTimestamp
    }));
}

// Добавление забронированных мест
function addBookedSeats(seatsToAdd) {
    var currentBooked = loadBookedSeats();
    var newBooked = currentBooked.slice();
    for (var i = 0; i < seatsToAdd.length; i++) {
        if (newBooked.indexOf(seatsToAdd[i]) === -1) {
            newBooked.push(seatsToAdd[i]);
        }
    }
    var expiry = Date.now() + 60000;
    saveBookedSeats(newBooked, expiry);
    return newBooked;
}

// Освобождение мест
function releaseBookedSeats(seatsToRelease) {
    var currentBooked = loadBookedSeats();
    var newBooked = [];
    for (var i = 0; i < currentBooked.length; i++) {
        if (seatsToRelease.indexOf(currentBooked[i]) === -1) {
            newBooked.push(currentBooked[i]);
        }
    }
    if (newBooked.length > 0) {
        var expiry = Date.now() + 60000;
        saveBookedSeats(newBooked, expiry);
    } else {
        localStorage.removeItem(getBookedSeatsKey());
    }
    return newBooked;
}

// Функция выбора места
window.toggleSeat = function (seatId, isVip, canSelect) {
    if (!canSelect) return;

    var index = selectedSeats.indexOf(seatId);
    if (index >= 0) {
        selectedSeats.splice(index, 1);
    } else {
        selectedSeats.push(seatId);
    }

    renderHall();
};

// Обновление сводки
function updateSummary() {
    var params = getParamsFromURL();
    var count = selectedSeats.length;
    var total = 0;
    var selectedList = [];

    for (var i = 0; i < selectedSeats.length; i++) {
        var seatId = selectedSeats[i];
        var isVip = false;
        if (hallsConfig && hallsConfig[params.hallId]) {
            isVip = hallsConfig[params.hallId].vipSeats.indexOf(seatId) !== -1;
        }
        var price = isVip ? params.price * 1.5 : params.price;
        total += price;
        selectedList.push(seatId);
    }

    var countEl = document.getElementById('selected-count');
    var listEl = document.getElementById('selected-list');
    var totalEl = document.getElementById('total-price');
    var buyBtn = document.getElementById('buy-btn');

    if (countEl) countEl.innerText = count;
    if (listEl) listEl.innerText = selectedList.join(', ');
    if (totalEl) totalEl.innerText = total + ' ₽';
    if (buyBtn) buyBtn.disabled = count === 0;
}

// Переход к оплате
window.proceedToPayment = function () {
    if (selectedSeats.length === 0) return;

    addBookedSeats(selectedSeats);

    var params = getParamsFromURL();
    var movie = null;
    if (typeof getMovieById === 'function') {
        movie = getMovieById(params.movieId);
    }
    var total = 0;
    for (var i = 0; i < selectedSeats.length; i++) {
        var seatId = selectedSeats[i];
        var isVip = false;
        if (hallsConfig && hallsConfig[params.hallId]) {
            isVip = hallsConfig[params.hallId].vipSeats.indexOf(seatId) !== -1;
        }
        total += isVip ? params.price * 1.5 : params.price;
    }

    showPaymentModal(movie ? movie.title : 'Фильм', params, total);
};

// Отмена оплаты
window.cancelPayment = function () {
    clearInterval(timerInterval);
    closePaymentModal();
    releaseBookedSeats(selectedSeats);
    alert('Бронирование отменено. Места снова доступны для выбора.');
    selectedSeats = [];
    renderHall();
};

// Подтверждение оплаты
window.confirmPayment = function () {
    var email = document.getElementById('user-email');
    if (!email || !email.value || email.value.indexOf('@') === -1) {
        alert('Пожалуйста, введите корректный email');
        return;
    }

    clearInterval(timerInterval);
    closePaymentModal();

    alert('Билеты успешно оплачены!\n\nБилеты отправлены на ' + email.value + '\nМеста: ' + selectedSeats.join(', ') + '\n\nСпасибо за покупку!');

    var bookedSeats = loadBookedSeats();
    var expiry = Date.now() + 86400000;
    saveBookedSeats(bookedSeats, expiry);

    selectedSeats = [];
    renderHall();
};

// Показ модального окна оплаты
function showPaymentModal(movieTitle, params, total) {
    isPaymentModalOpen = true;
    startTimer();

    var modalHtml = '<div id="payment-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 2000;">' +
        '<div style="background: white; border-radius: 20px; max-width: 500px; width: 90%; padding: 30px;">' +
        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">' +
        '<h3 style="color: #280595; margin:0;">Оформление билетов</h3>' +
        '<div id="payment-timer" style="font-size: 24px; font-weight: 700; color: #ff4757; background: #fff5f5; padding: 8px 16px; border-radius: 40px;">01:00</div>' +
        '</div>' +
        '<div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px;">' +
        '<p style="font-weight: 700; margin-bottom: 10px;">' + movieTitle + '</p>' +
        '<p style="color: #666; margin-bottom: 5px;">' + params.hallName + ' | ' + params.time + '</p>' +
        '<p style="color: #666; margin-bottom: 5px;">Места: ' + selectedSeats.join(', ') + '</p>' +
        '<p style="color: #666; margin-bottom: 10px;">Количество: ' + selectedSeats.length + '</p>' +
        '<p style="font-size: 24px; font-weight: 700; color: #3A4DB3;">Итого: ' + total + ' ₽</p>' +
        '</div>' +
        '<div style="margin-bottom: 20px;">' +
        '<div style="margin-bottom: 15px;">' +
        '<label style="display: block; margin-bottom: 5px; font-weight: 500;">Email для получения билетов</label>' +
        '<input type="email" id="user-email" placeholder="your@email.com" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box;">' +
        '</div>' +
        '<div>' +
        '<label style="display: block; margin-bottom: 5px; font-weight: 500;">Способ оплаты</label>' +
        '<select id="payment-method" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box;">' +
        '<option value="card">Банковская карта</option>' +
        '<option value="sbp">СБП</option>' +
        '<option value="cash">Наличные в кассе</option>' +
        '</select>' +
        '</div>' +
        '</div>' +
        '<div style="display: flex; gap: 15px;">' +
        '<button onclick="window.cancelPayment()" style="flex: 1; padding: 12px; background: #f0f0f0; border: none; border-radius: 40px; font-weight: 600; cursor: pointer;">Отмена</button>' +
        '<button onclick="window.confirmPayment()" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #3A4DB3, #280595); color: white; border: none; border-radius: 40px; font-weight: 600; cursor: pointer;">Оплатить ' + total + ' ₽</button>' +
        '</div>' +
        '</div>' +
        '</div>';

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    document.body.style.overflow = 'hidden';
}

function startTimer() {
    timeLeft = 60;

    if (timerInterval) clearInterval(timerInterval);

    timerInterval = setInterval(function () {
        timeLeft--;
        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;
        var minutesStr = minutes < 10 ? '0' + minutes : '' + minutes;
        var secondsStr = seconds < 10 ? '0' + seconds : '' + seconds;
        var timerEl = document.getElementById('payment-timer');
        if (timerEl) {
            timerEl.textContent = minutesStr + ':' + secondsStr;
        }

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            window.cancelPayment();
        }
    }, 1000);
}

function closePaymentModal() {
    var modal = document.getElementById('payment-modal');
    if (modal) modal.remove();
    document.body.style.overflow = '';
    isPaymentModalOpen = false;
}

// Рендер зала
function renderHall() {
    var params = getParamsFromURL();
    var container = document.getElementById('hall-container');

    if (typeof moviesDatabase === 'undefined') {
        container.innerHTML = '<div class="not-found"><h2>Ошибка</h2><p>База данных не загружена</p><a href="index.html" class="back-link">← На главную</a></div>';
        return;
    }

    var movie = getMovieById(params.movieId);
    var hallConfig = null;
    if (typeof hallsConfig !== 'undefined') {
        hallConfig = hallsConfig[params.hallId];
    }

    if (!movie || !hallConfig) {
        container.innerHTML = '<div class="not-found"><h2>Ошибка</h2><p>Сеанс не найден</p><a href="index.html" class="back-link">← На главную</a></div>';
        return;
    }

    var bookedSeats = loadBookedSeats();

    var seatsHtml = '';
    for (var row = 1; row <= hallConfig.rows; row++) {
        seatsHtml += '<div class="seat-row">';
        for (var seat = 1; seat <= hallConfig.seatsPerRow; seat++) {
            var seatId = row + '-' + seat;
            var isVip = hallConfig.vipSeats.indexOf(seatId) !== -1;
            var isDisabled = hallConfig.disabledSeats.indexOf(seatId) !== -1;
            var isBooked = bookedSeats.indexOf(seatId) !== -1;
            var isSelected = selectedSeats.indexOf(seatId) !== -1;

            var seatClass = 'seat';
            if (isVip) seatClass += ' vip';
            if (isDisabled) seatClass += ' wheelchair';
            if (isBooked) seatClass += ' disabled-seat';
            if (isSelected) seatClass += ' selected';

            var seatText = seat;
            if (isDisabled) seatText = '♿';

            var canSelect = (!isBooked && !isSelected) ? 'true' : 'false';

            seatsHtml += '<div class="' + seatClass + '" data-seat="' + seatId + '" data-vip="' + isVip + '" onclick="window.toggleSeat(\'' + seatId + '\', ' + isVip + ', ' + canSelect + ')">' + seatText + '</div>';
        }
        seatsHtml += '</div>';
    }

    container.innerHTML = `
        <a href="movie.html?id=${params.movieId}" class="back-link">← Назад к фильму</a>
        
        <div class="session-info">
            <h2>${movie.title}</h2>
            <div class="session-details">
                <span>${params.hallName}</span>
                <span>${params.time}</span>
                <span>${params.price}₽ / место</span>
                <span>VIP места: ${Math.round(params.price * 1.5)}₽</span>
            </div>
        </div>
        
        <div class="screen">
            <div class="screen-line"></div>
            <span class="screen-label">ЭКРАН</span>
        </div>
        
        <div class="seats-container">
            <div class="seats-grid">
                ${seatsHtml}
            </div>
        </div>
        
        <div class="seat-info">
            <div class="info-item"><div class="info-dot available"></div><span>Свободно</span></div>
            <div class="info-item"><div class="info-dot vip"></div><span>VIP место</span></div>
            <div class="info-item"><div class="info-dot selected"></div><span>Выбрано</span></div>
            <div class="info-item"><div class="info-dot disabled"></div><span>Забронировано</span></div>
            <div class="info-item"><div class="info-dot wheelchair"></div><span>Для инвалидов</span></div>
        </div>
        
        <div class="selection-summary">
            <div class="selected-info">
                <span>Выбрано мест: <span id="selected-count">0</span></span>
                <span class="selected-seats" id="selected-list"></span>
            </div>
            <div class="total-price" id="total-price">0 ₽</div>
            <button class="buy-btn" id="buy-btn" onclick="window.proceedToPayment()" disabled>Купить билеты</button>
        </div>
    `;

    updateSummary();
}

// Проверка истекших броней
setInterval(function () {
    var key = getBookedSeatsKey();
    var saved = localStorage.getItem(key);
    if (saved) {
        try {
            var parsed = JSON.parse(saved);
            if (parsed.expiry < Date.now()) {
                localStorage.removeItem(key);
                if (window.location.pathname.indexOf('hall.html') !== -1 && !isPaymentModalOpen) {
                    selectedSeats = [];
                    renderHall();
                }
            }
        } catch (e) { }
    }
}, 5000);

// Загрузка страницы
document.addEventListener('DOMContentLoaded', function () {
    renderHall();
});