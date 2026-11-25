@extends('layouts.app')

@section('title', $movie->title . ' — Сливкино')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/movie-page.css') }}">
@endpush

@section('content')
<div class="movie-detail-container">
    <a href="{{ route('home') }}" class="back-link">← Вернуться к афише</a>
    <div class="movie-detail-poster">
        @if ($movie->poster)
            <img src="{{ asset($movie->poster) }}" alt="Постер {{ $movie->title }}">
        @else
            <div class="poster-placeholder">🎬</div>
        @endif
    </div>
    <div class="movie-detail-info">
        <h1>{{ $movie->title }}</h1>
        @if ($movie->title_en)<p>{{ $movie->title_en }}</p>@endif
        <p>{{ $movie->year }} · {{ $movie->country }} · {{ $movie->age_rating }} @if ($movie->duration_minutes) · {{ $movie->duration_minutes }} мин.@endif</p>
        <p>{{ $movie->genres->pluck('name')->implode(', ') }}</p>
        <p>{{ $movie->full_description ?: $movie->description }}</p>
        @if ($movie->director)<p><strong>Режиссёр:</strong> {{ $movie->director }}</p>@endif
        @if ($movie->cast)<p><strong>В ролях:</strong> {{ implode(', ', $movie->cast) }}</p>@endif
    </div>
</div>

<section class="movie-sessions-section">
    <h2 class="section-title">Сеансы</h2>
    @forelse ($movie->sessions->sortBy('starts_at') as $session)
        <article class="movie-session-card">
            <div>
                <h3>{{ $session->hall->cinema->name }}</h3>
                <p>{{ $session->hall->cinema->address }}</p>
                <p>{{ $session->hall->name }} · {{ $session->starts_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="movie-session-action">
                <strong>{{ number_format((float) $session->price, 0, ',', ' ') }} ₽</strong>
                <a href="{{ route('sessions.seats', $session) }}">Выбрать места</a>
            </div>
        </article>
    @empty
        <p class="empty-state">Для этого фильма пока нет опубликованных сеансов.</p>
    @endforelse
</section>
@endsection

@push('styles')
<style>
    .movie-detail-container { display:grid; grid-template-columns:minmax(220px, 320px) 1fr; gap:30px; align-items:start; }
    .movie-detail-poster img { display:block; width:100%; max-height:460px; object-fit:contain; }
    .movie-sessions-section { margin:30px 0 50px; }
    .movie-session-card { display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; background:#fff; padding:18px 22px; margin:0 0 12px; box-shadow:0 3px 14px rgba(0,0,0,.06); }
    .movie-session-card h3 { margin:0 0 6px; color:#280595; }
    .movie-session-card p { margin:4px 0; color:#666; }
    .movie-session-action { display:flex; align-items:center; gap:14px; }
    .movie-session-action strong { font-size:20px; }
    .movie-session-action a { display:inline-block; padding:10px 18px; border-radius:24px; background:#3A4DB3; color:#fff; text-decoration:none; font-weight:600; }
    .empty-state { padding:24px; color:#666; background:#fff; }
    @media(max-width:700px) { .movie-detail-container { grid-template-columns:1fr; } .movie-detail-poster img { max-width:260px; margin:auto; } }
</style>
@endpush
