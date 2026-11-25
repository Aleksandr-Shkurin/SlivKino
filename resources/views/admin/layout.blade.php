<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Администрирование') — Сливкино</title>
    <style>
        :root{--ink:#20243a;--muted:#70758b;--line:#e6e8f0;--bg:#f5f6fb;--brand:#3a4db3;--brand-dark:#293a96;--danger:#b42318;--green:#16794b}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 Roboto,Arial,sans-serif}
        a{color:var(--brand);text-decoration:none}a:hover{text-decoration:underline}.admin-shell{min-height:100vh;display:grid;grid-template-columns:250px minmax(0,1fr)}
        .sidebar{background:#202746;color:#fff;padding:26px 18px;display:flex;flex-direction:column;gap:22px}.brand{font-size:24px;font-weight:800;color:#fff;letter-spacing:.2px}.brand small{display:block;font-size:10px;letter-spacing:2px;color:#bfc7ed;font-weight:600}.side-label{font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#aeb8df;margin:8px 10px 2px}.side-nav{display:grid;gap:5px}.side-nav a{padding:10px 12px;border-radius:10px;color:#e9ecfa;display:flex;gap:10px}.side-nav a:hover,.side-nav a.active{background:#35416f;text-decoration:none;color:#fff}.side-bottom{margin-top:auto;display:grid;gap:10px}.side-bottom a,.side-bottom button{color:#e9ecfa;background:transparent;border:0;text-align:left;padding:10px 12px;font:inherit;cursor:pointer;border-radius:9px}.side-bottom a:hover,.side-bottom button:hover{background:#35416f;text-decoration:none}
        .main{min-width:0}.topbar{height:70px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 34px}.topbar strong{font-size:16px}.topbar span{color:var(--muted);font-size:13px}.content{padding:30px 34px;max-width:1500px;margin:auto}.page-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:22px}.page-head h1{font-size:27px;line-height:1.2;margin:0}.page-head p{color:var(--muted);margin:7px 0 0}.btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:9px;padding:10px 15px;background:var(--brand);color:#fff;font-weight:700;cursor:pointer;font-size:14px}.btn:hover{background:var(--brand-dark);color:#fff;text-decoration:none}.btn-light{background:#edf0ff;color:var(--brand)}.btn-danger{background:#fff0ef;color:var(--danger)}.btn-small{padding:7px 10px;font-size:12px}.panel,.stat-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 4px 14px rgba(24,34,75,.035)}.panel{padding:20px;margin-bottom:20px}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:22px}.stat-card{padding:19px}.stat-card span{color:var(--muted);font-size:13px}.stat-card strong{display:block;font-size:29px;margin-top:5px}.stat-card a{font-size:12px}.table-wrap{overflow:auto}.data-table{width:100%;border-collapse:collapse;min-width:650px}.data-table th,.data-table td{padding:12px 13px;border-bottom:1px solid var(--line);text-align:left;vertical-align:middle}.data-table th{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;background:#fafbfe}.data-table td{font-size:14px}.data-table tr:last-child td{border-bottom:0}.actions{display:flex;gap:7px;align-items:center;flex-wrap:wrap}.actions form{display:inline}.muted{color:var(--muted)}.pill{display:inline-block;background:#edf0ff;color:#3446a6;border-radius:20px;padding:3px 9px;font-size:12px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.field{display:grid;gap:6px}.field.full{grid-column:1/-1}.field label{font-size:13px;font-weight:700}.field input,.field textarea,.field select{width:100%;border:1px solid #d9ddea;border-radius:9px;padding:10px 12px;font:inherit;background:#fff;color:var(--ink)}.field textarea{min-height:105px;resize:vertical}.field input:focus,.field textarea:focus,.field select:focus{outline:3px solid #e2e7ff;border-color:#8592df}.help{font-size:12px;color:var(--muted)}.form-actions{display:flex;gap:10px;align-items:center;margin-top:20px}.alert{padding:13px 15px;border-radius:10px;margin-bottom:17px}.alert-success{background:#eaf8f0;color:#17643e}.alert-error{background:#fff0ef;color:#9e271e}.alert ul{margin:5px 0 0 18px}.inline-form{display:flex;gap:8px;align-items:center;min-width:220px}.inline-form input,.inline-form select{min-width:0;border:1px solid #d9ddea;border-radius:8px;padding:8px}.pagination{margin-top:18px}.empty{padding:25px;text-align:center;color:var(--muted)}.login-page{min-height:100vh;display:grid;place-items:center;padding:20px;background:linear-gradient(135deg,#202746,#3b4eb5)}.login-card{width:min(100%,430px);background:#fff;border-radius:18px;padding:32px;box-shadow:0 25px 70px #11193655}.login-card h1{margin:0 0 6px}.login-card p{color:var(--muted);margin:0 0 24px}.login-card .field{margin:14px 0}.login-card .btn{width:100%;margin-top:8px;padding:13px}.public-link{display:inline-block;margin-top:15px;font-size:13px}
        @media(max-width:900px){.admin-shell{grid-template-columns:1fr}.sidebar{padding:14px 16px;gap:10px}.brand small,.side-label,.side-bottom{display:none}.side-nav{display:flex;overflow:auto}.side-nav a{white-space:nowrap;padding:8px 10px}.topbar{height:55px;padding:0 18px}.content{padding:22px 16px}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:560px){.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.page-head{align-items:flex-start;flex-direction:column}.stats{grid-template-columns:1fr 1fr}.stat-card strong{font-size:24px}.topbar span{display:none}}
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">Сливкино<small>ПАНЕЛЬ УПРАВЛЕНИЯ</small></a>
        <div><div class="side-label">Управление каталогом</div><nav class="side-nav">
            <a href="{{ route('admin.dashboard') }}">◫ Обзор</a>
            <a href="{{ route('admin.movies.index') }}">▣ Фильмы</a>
            <a href="{{ route('admin.genres.index') }}">◈ Жанры</a>
            <a href="{{ route('admin.cinemas.index') }}">⌂ Кинотеатры</a>
            <a href="{{ route('admin.halls.index') }}">▤ Залы и VIP-места</a>
            <a href="{{ route('admin.sessions.index') }}">◷ Сеансы</a>
            <a href="{{ route('admin.bookings.index') }}">▣ Бронирования</a>
        </nav></div>
        <div class="side-bottom">
            <a href="{{ route('home') }}" target="_blank">↗ Открыть сайт</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">⇥ Выйти из панели</button></form>
        </div>
    </aside>
    <div class="main">
        <header class="topbar"><strong>@yield('section-title', 'Обзор')</strong><span>Вы вошли как {{ auth()->user()->name }}</span></header>
        <main class="content">
            @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="alert alert-error"><strong>Не удалось выполнить действие:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
