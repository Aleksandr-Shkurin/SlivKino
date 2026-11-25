<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Сливкино — Афиша кино')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
<div class="app">
    <header class="header">
        <a href="{{ route('home') }}" class="logo">Сливкино</a>
        <nav class="sub-nav" aria-label="Основная навигация">
            <a href="{{ route('home') }}">Афиша</a>
            <a href="{{ route('cinemas.index') }}">Кинотеатры</a>
            <a href="{{ route('admin.login') }}">Администрирование</a>
        </nav>
    </header>
    <main>@yield('content')</main>
</div>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand"><a href="{{ route('home') }}" class="footer-logo">Сливкино<span>КИНО В ТВОЁМ ГОРОДЕ</span></a><p>Выбирайте фильмы, кинотеатры и удобные сеансы в одном месте.</p></div>
        <nav class="footer-links"><a href="{{ route('home') }}">Афиша</a><a href="{{ route('cinemas.index') }}">Кинотеатры</a></nav>
        <div class="footer-note">Афиша кино</div>
    </div>
    <div class="footer-bottom"><span>© {{ date('Y') }} Сливкино</span><span>Кино в твоём городе</span></div>
</footer>
</body>
</html>
