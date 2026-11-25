@extends('layouts.app')

@section('title', 'Сливкино — Афиша кино')

@section('content')
<section class="search-filter-section" aria-label="Поиск фильмов">
    <form class="movie-search-form" method="GET" action="{{ route('home') }}">
        <div class="search-box">
            <label class="visually-hidden" for="movie-search">Название фильма</label>
            <input
                id="movie-search"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Поиск по названию фильма..."
                autocomplete="off"
            >
        </div>
        <div class="genre-filter">
            <label class="visually-hidden" for="genre-filter">Жанр фильма</label>
            <select id="genre-filter" name="genre">
                <option value="">Все жанры</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" @selected($genreId === $genre->id)>
                        {{ mb_convert_case($genre->name, MB_CASE_TITLE, 'UTF-8') }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="filter-submit" type="submit">Найти</button>
        @if ($search !== '' || $genreId)
            <a class="filter-reset" href="{{ route('home') }}">Сбросить</a>
        @endif
    </form>
</section>

@if ($search !== '' || $genreId)
    <p class="search-result-caption">
        Результаты поиска
        @if ($search !== '') для «{{ $search }}»@endif
        @if ($genreId && $genres->firstWhere('id', $genreId))
            · жанр: {{ $genres->firstWhere('id', $genreId)->name }}
        @endif
    </p>
@endif

<section>
    <h2 class="section-title">Сейчас в кино</h2>
    <div class="movies-grid" id="now-playing-grid">
        @forelse ($nowPlaying as $movie)
            <a class="movie-card" href="{{ route('movies.show', $movie) }}">
                <div class="movie-poster">
                    @if ($movie->poster)
                        <img src="{{ asset($movie->poster) }}" alt="Постер фильма {{ $movie->title }}">
                    @else
                        <div class="poster-placeholder">🎬</div>
                    @endif
                </div>
                <div class="movie-info">
                    <h3 class="movie-title">{{ $movie->title }}</h3>
                    <p class="movie-meta"><span>{{ $movie->year }}</span><span>{{ $movie->age_rating }}</span></p>
                    <p>{{ $movie->genres->pluck('name')->implode(', ') }}</p>
                </div>
            </a>
        @empty
            <p class="empty-state">Подходящих фильмов в текущем прокате не найдено.</p>
        @endforelse
    </div>
</section>

<section class="coming-soon">
    <h2 class="section-title">Скоро в кино</h2>
    <div class="movies-grid">
        @forelse ($comingSoon as $movie)
            <a class="movie-card" href="{{ route('movies.show', $movie) }}">
                <div class="movie-poster">
                    @if ($movie->poster)
                        <img src="{{ asset($movie->poster) }}" alt="Постер фильма {{ $movie->title }}">
                    @else
                        <div class="poster-placeholder">🎬</div>
                    @endif
                </div>
                <div class="movie-info">
                    <h3 class="movie-title">{{ $movie->title }}</h3>
                    <p>{{ $movie->release_date?->format('d.m.Y') }}</p>
                    <p>{{ $movie->genres->pluck('name')->implode(', ') }}</p>
                </div>
            </a>
        @empty
            <p class="empty-state">Подходящих премьер пока нет.</p>
        @endforelse
    </div>
</section>
@endsection

@push('styles')
<style>
    .movie-search-form { display:flex; flex-wrap:wrap; align-items:center; gap:12px; width:100%; }
    .movie-search-form .search-box { flex:2 1 260px; }
    .movie-search-form .genre-filter { flex:1 1 210px; }
    .filter-submit, .filter-reset { border:0; border-radius:30px; padding:12px 24px; font:inherit; font-weight:600; text-decoration:none; cursor:pointer; }
    .filter-submit { color:#fff; background:#3A4DB3; }
    .filter-submit:hover { background:#280595; }
    .filter-reset { color:#3A4DB3; background:#e8eaf6; }
    .search-result-caption { margin:8px 0 16px; color:#666; }
    .empty-state { padding:24px; color:#666; background:#fff; }
    .visually-hidden { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
</style>
@endpush
