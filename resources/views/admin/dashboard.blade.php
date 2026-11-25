@extends('admin.layout')
@section('title', 'Обзор')
@section('section-title', 'Обзор проекта')
@section('content')
<div class="page-head"><div><h1>Панель управления</h1><p>Основные данные сайта «Сливкино» и быстрый доступ к редактированию.</p></div></div>
<div class="stats">
    @foreach ([['Фильмы','movies','admin.movies.index'],['Жанры','genres','admin.genres.index'],['Кинотеатры','cinemas','admin.cinemas.index'],['Залы','halls','admin.halls.index'],['Сеансы','sessions','admin.sessions.index'],['Бронирования','bookings','admin.bookings.index']] as [$label,$key,$routeName])
        <div class="stat-card"><span>{{ $label }}</span><strong>{{ $counts[$key] }}</strong><a href="{{ route($routeName) }}">Перейти к управлению →</a></div>
    @endforeach
</div>
<section class="panel"><div class="page-head"><div><h2 style="font-size:18px;margin:0">Последние бронирования</h2><p>Последние восемь записей в базе данных.</p></div><a class="btn btn-light" href="{{ route('admin.bookings.index') }}">Все бронирования</a></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>№</th><th>Клиент</th><th>Фильм / сеанс</th><th>Места</th><th>Статус</th></tr></thead><tbody>
@forelse ($latestBookings as $booking)<tr><td>#{{ $booking->id }}</td><td>{{ $booking->customer_name }}<br><span class="muted">{{ $booking->customer_email }}</span></td><td>{{ $booking->session?->movie?->title ?? '—' }}<br><span class="muted">{{ $booking->session?->starts_at?->format('d.m.Y H:i') }}</span></td><td>{{ implode(', ', $booking->seats ?? []) }}</td><td><span class="pill">{{ $booking->status }}</span></td></tr>@empty<tr><td colspan="5" class="empty">Бронирований пока нет.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
