@extends('layouts.app')

@section('title', $cinema->name . ' — Сливкино')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cinema-detail.css') }}">
@endpush

@section('content')
<div class="cinema-detail-container">
    <a href="{{ route('cinemas.index') }}" class="back-link">← Назад к списку кинотеатров</a>

    <article class="cinema-detail-card">
        <header class="cinema-detail-header">
            <h1 class="cinema-detail-name">{{ $cinema->name }}</h1>
            <p class="cinema-detail-address">{{ $cinema->address }}</p>
        </header>

        <div class="cinema-detail-content">
            <div class="info-grid">
                <section class="info-card">
                    <h3>Контакты</h3>
                    @if ($cinema->phone)<p class="phone">{{ $cinema->phone }}</p>@endif
                    @if ($cinema->email)<p>{{ $cinema->email }}</p>@endif
                    @if (!$cinema->phone && !$cinema->email)<p>Контактная информация пока не указана.</p>@endif
                </section>
                <section class="info-card">
                    <h3>Режим работы</h3>
                    <p>{{ $cinema->working_hours ?: 'Информация уточняется.' }}</p>
                </section>
                <section class="info-card">
                    <h3>Оснащение</h3>
                    @if ($cinema->features)
                        <div class="features-list">
                            @foreach ($cinema->features as $feature)
                                <span class="feature-tag">{{ $feature }}</span>
                            @endforeach
                        </div>
                    @else
                        <p>Сведения об оснащении пока не добавлены.</p>
                    @endif
                </section>
            </div>

            <section class="description-section">
                <h2>О кинотеатре</h2>
                <p>{{ $cinema->description ?: 'Описание кинотеатра скоро появится.' }}</p>
            </section>

            <section class="cinema-halls-section">
                <h2>Залы и сеансы</h2>
                @forelse ($cinema->halls as $hall)
                    <article class="cinema-hall-card">
                        <div class="cinema-hall-heading">
                            <h3>{{ $hall->name }}</h3>
                            <span>{{ $hall->rows_count }} рядов · {{ $hall->seats_per_row }} мест в ряду</span>
                        </div>

                        @if ($hall->sessions->isNotEmpty())
                            <div class="hall-session-list">
                                @foreach ($hall->sessions as $session)
                                    <div class="hall-session-item">
                                        <div>
                                            <a class="hall-session-movie" href="{{ route('movies.show', $session->movie) }}">{{ $session->movie->title }}</a>
                                            <p>{{ $session->starts_at->format('d.m.Y, H:i') }} · {{ number_format((float) $session->price, 0, ',', ' ') }} ₽</p>
                                        </div>
                                        <a class="hall-session-button" href="{{ route('sessions.seats', $session) }}">Выбрать места</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="no-hall-sessions">В этом зале пока нет опубликованных сеансов.</p>
                        @endif
                    </article>
                @empty
                    <p>Информация о залах пока не добавлена.</p>
                @endforelse
            </section>
        </div>
    </article>
</div>
@endsection

@push('styles')
<style>
    .cinema-halls-section { margin-top:32px; }
    .cinema-halls-section > h2 { color:#280595; font-size:24px; margin:0 0 18px; }
    .cinema-hall-card { border:1px solid #e4e5ef; background:#fff; padding:20px; margin:0 0 16px; }
    .cinema-hall-heading { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px; }
    .cinema-hall-heading h3 { color:#280595; margin:0; }
    .cinema-hall-heading span, .no-hall-sessions { color:#777; }
    .hall-session-list { display:flex; flex-direction:column; gap:10px; }
    .hall-session-item { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:13px 15px; background:#f7f7fb; }
    .hall-session-movie { color:#280595; font-weight:700; text-decoration:none; }
    .hall-session-item p { margin:5px 0 0; color:#666; }
    .hall-session-button { display:inline-block; padding:9px 16px; border-radius:22px; background:#3A4DB3; color:#fff; text-decoration:none; font-weight:600; }
    .hall-session-button:hover { background:#280595; }
</style>
@endpush
