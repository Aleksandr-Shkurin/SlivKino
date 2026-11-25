@extends('layouts.app')

@section('title', 'Выбор мест — ' . $session->movie->title . ' — Сливкино')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hall.css') }}">
@endpush

@section('content')
<div class="hall-container">
    <a href="{{ route('movies.show', $session->movie) }}" class="back-link">← Назад к фильму</a>

    <section class="session-info">
        <h1>{{ $session->movie->title }}</h1>
        <div class="session-details">
            <span>{{ $session->hall->cinema->name }}</span>
            <span>{{ $session->hall->name }}</span>
            <span>{{ $session->starts_at->format('d.m.Y H:i') }}</span>
            <span>Обычное место: {{ number_format((float) $session->price, 0, ',', ' ') }} ₽</span>
            <span>VIP: {{ number_format((float) $session->price * 1.5, 0, ',', ' ') }} ₽</span>
        </div>
    </section>

    @if (session('success'))
        <div class="booking-success" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="booking-errors" role="alert">
            <strong>Не удалось оформить бронирование:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="screen">
        <div class="screen-line"></div>
        <span class="screen-label">ЭКРАН</span>
    </div>

    <form method="POST" action="{{ route('sessions.bookings.store', $session) }}" id="booking-form">
        @csrf
        <div class="seats-container">
            <div class="seats-grid" aria-label="Схема мест в зале">
                @for ($row = 1; $row <= $hall->rows_count; $row++)
                    <div class="seat-row">
                        @for ($number = 1; $number <= $hall->seats_per_row; $number++)
                            @php
                                $seatId = $row . '-' . $number;
                                $isVip = in_array($seatId, $vipSeats, true);
                                $isWheelchair = in_array($seatId, $wheelchairSeats, true);
                                $isOccupied = in_array($seatId, $occupiedSeats, true);
                                $isUnavailable = $isOccupied;
                                $seatPrice = (float) $session->price * ($isVip ? 1.5 : 1);
                            @endphp
                            <label class="seat-label {{ $isVip ? 'is-vip' : '' }} {{ $isUnavailable ? 'is-unavailable' : '' }} {{ $isWheelchair ? 'is-wheelchair' : '' }}" title="Ряд {{ $row }}, место {{ $number }}{{ $isVip ? ' — VIP' : '' }}{{ $isWheelchair ? ' — место для маломобильных посетителей' : '' }}">
                                <input
                                    type="checkbox"
                                    name="seats[]"
                                    value="{{ $seatId }}"
                                    data-price="{{ $seatPrice }}"
                                    @disabled($isUnavailable)
                                    @checked(in_array($seatId, old('seats', []), true) && !$isUnavailable)
                                >
                                <span>{{ $isWheelchair ? '♿' : $number }}</span>
                            </label>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>

        <div class="seat-info">
            <div class="info-item"><span class="info-dot available"></span><span>Свободно</span></div>
            <div class="info-item"><span class="info-dot vip"></span><span>VIP</span></div>
            <div class="info-item"><span class="info-dot selected"></span><span>Выбрано</span></div>
            <div class="info-item"><span class="info-dot disabled"></span><span>Занято</span></div>
            <div class="info-item"><span class="info-dot wheelchair"></span><span>Для маломобильных посетителей</span></div>
        </div>

        <section class="booking-panel">
            <div class="booking-selection">
                <strong>Выбрано мест: <span id="selected-count">0</span></strong>
                <p>Места: <span id="selected-list">не выбраны</span></p>
                <strong>Итого: <span id="total-price">0 ₽</span></strong>
            </div>
            <div class="booking-customer-fields">
                <label>Ваше имя
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" maxlength="255" required>
                </label>
                <label>Email для подтверждения
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" maxlength="255" required>
                </label>
            </div>
            <button class="buy-btn" id="buy-btn" type="submit" disabled>Забронировать места</button>
            <p class="booking-note">Бронирование сохраняется в базе данных на 15 минут. Это демонстрационное бронирование, онлайн-оплата не подключена.</p>
        </section>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('booking-form');
    const checkboxes = Array.from(form.querySelectorAll('input[name="seats[]"]'));
    const count = document.getElementById('selected-count');
    const list = document.getElementById('selected-list');
    const total = document.getElementById('total-price');
    const button = document.getElementById('buy-btn');

    function updateSummary() {
        const selected = checkboxes.filter((checkbox) => checkbox.checked);
        const seats = selected.map((checkbox) => checkbox.value);
        const sum = selected.reduce((value, checkbox) => value + Number(checkbox.dataset.price || 0), 0);
        count.textContent = String(selected.length);
        list.textContent = seats.length ? seats.join(', ') : 'не выбраны';
        total.textContent = new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 }).format(sum) + ' ₽';
        button.disabled = selected.length === 0;
    }

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSummary));
    updateSummary();
});
</script>
@endsection

@push('styles')
<style>
    .seat-label { position:relative; display:inline-flex; width:40px; height:40px; flex:0 0 40px; cursor:pointer; }
    .seat-label input { position:absolute; opacity:0; width:1px; height:1px; }
    .seat-label span { display:flex; width:100%; height:100%; align-items:center; justify-content:center; border:2px solid transparent; border-radius:8px; background:#e8eaf6; color:#333; font-size:12px; font-weight:600; transition:.15s; }
    .seat-label:hover span { background:#c5cae9; transform:scale(1.05); }
    .seat-label.is-vip span { background:linear-gradient(135deg,#ffd700,#ffed4e); color:#280595; }
    .seat-label input:checked + span { background:#4caf50; color:white; border-color:#2e7d32; }
    .seat-label.is-unavailable { cursor:not-allowed; }
    .seat-label.is-unavailable span { background:#f44336; color:white; opacity:.75; }
    .seat-label.is-wheelchair span { background:#2196f3; color:white; }
    .seat-label.is-wheelchair input:checked + span { background:#4caf50; color:white; }
    .seat-label input:focus-visible + span { outline:3px solid #280595; outline-offset:3px; }
    .booking-success, .booking-errors { padding:15px 18px; margin:18px 0; border-radius:8px; }
    .booking-success { background:#e8f5e9; color:#1b5e20; }
    .booking-errors { background:#ffebee; color:#b71c1c; }
    .booking-errors ul { margin:8px 0 0 20px; }
    .booking-panel { display:grid; grid-template-columns:1fr 1fr auto; gap:18px; align-items:end; background:#fff; border:2px solid #3A4DB3; padding:20px; margin:25px 0 45px; box-shadow:0 4px 25px rgba(0,0,0,.1); }
    .booking-selection p { margin:8px 0; color:#555; }
    .booking-customer-fields { display:grid; gap:10px; }
    .booking-customer-fields label { display:grid; gap:5px; font-size:13px; color:#555; }
    .booking-customer-fields input { width:100%; min-width:0; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font:inherit; }
    .buy-btn:disabled { opacity:.5; cursor:not-allowed; }
    .booking-note { grid-column:1/-1; color:#777; font-size:12px; margin:0; }
    @media(max-width:760px) { .booking-panel { grid-template-columns:1fr; } .booking-note { grid-column:auto; } .seat-row { gap:5px; } .seat-label { width:32px; height:32px; flex-basis:32px; } }
</style>
@endpush
