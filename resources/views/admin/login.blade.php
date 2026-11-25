<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Вход администратора — Сливкино</title><style>
*{box-sizing:border-box}body{margin:0;font:15px/1.5 Arial,sans-serif;background:linear-gradient(135deg,#202746,#3a4db3);min-height:100vh;display:grid;place-items:center;padding:20px}.card{width:min(100%,420px);padding:32px;background:#fff;border-radius:18px;box-shadow:0 25px 70px #11193655}.brand{font-size:26px;font-weight:800;color:#202746}.eyebrow{font-size:10px;letter-spacing:2px;color:#3a4db3;font-weight:bold}.muted{color:#70758b;margin:8px 0 24px}.field{display:grid;gap:7px;margin:15px 0}.field label{font-size:13px;font-weight:bold}.field input{padding:12px;border:1px solid #d9ddea;border-radius:9px;font:inherit}.btn{width:100%;border:0;border-radius:9px;background:#3a4db3;color:white;padding:13px;font-weight:bold;font-size:15px;cursor:pointer;margin-top:8px}.error{background:#fff0ef;color:#9e271e;padding:11px;border-radius:8px;margin:12px 0}.back{display:block;text-align:center;margin-top:18px;color:#3a4db3;text-decoration:none;font-size:13px}
</style></head><body><section class="card"><div class="brand">Сливкино</div><div class="eyebrow">ПАНЕЛЬ УПРАВЛЕНИЯ</div><p class="muted">Войдите с учётной записью администратора.</p>
@if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
@if (session('success'))<div class="error" style="background:#eaf8f0;color:#17643e">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('admin.login.store') }}">@csrf
<div class="field"><label for="email">Электронная почта</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus></div>
<div class="field"><label for="password">Пароль</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<label style="font-size:13px;color:#70758b"><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
<button class="btn" type="submit">Войти</button></form><a class="back" href="{{ route('home') }}">← Вернуться на сайт</a></section></body></html>
