@extends('layouts.app')

@section('title', 'Кинотеатры — Сливкино')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cinemas.css') }}">
@endpush

@section('content')
<section class="cinemas-header">
    <h1 class="section-title">Наши кинотеатры</h1>
    <p class="cinemas-subtitle">Выберите кинотеатр, посмотрите описание, залы и доступные сеансы.</p>
</section>

<div class="cinemas-grid">
    @forelse ($cinemas as $cinema)
        <a href="{{ route('cinemas.show', $cinema) }}" class="cinema-card">
            <div class="cinema-preview">
                <div class="cinema-preview-placeholder">🎬</div>
            </div>
            <div class="cinema-info">
                <h2 class="cinema-name">{{ $cinema->name }}</h2>
                <p class="cinema-address">{{ $cinema->address }}</p>
                @if ($cinema->features)
                    <div class="cinema-features">
                        @foreach (array_slice($cinema->features, 0, 4) as $feature)
                            <span class="feature-tag">{{ $feature }}</span>
                        @endforeach
                    </div>
                @endif
                @if ($cinema->working_hours)
                    <p class="cinema-hours">{{ $cinema->working_hours }}</p>
                @endif
                <p><strong>Залов:</strong> {{ $cinema->halls_count }}</p>
                <span class="cinema-open-link">Подробнее о кинотеатре →</span>
            </div>
        </a>
    @empty
        <p class="no-movies">Кинотеатры пока не добавлены.</p>
    @endforelse
</div>
@endsection

@push('styles')
<style>
    .cinema-open-link { display:inline-block; margin-top:14px; color:#3A4DB3; font-weight:700; }
    .cinema-card:hover { border-color:#3A4DB3; }
</style>
@endpush
