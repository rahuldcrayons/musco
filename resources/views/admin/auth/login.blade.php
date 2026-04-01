<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign In - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|playfair-display:600,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F0EE; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .logo { margin-bottom: 20px; text-align: center; }
        .logo img { height: 36px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 24px 28px; width: 100%; max-width: 360px; }
        .card h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 600; color: #2D2D2D; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #222222; margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 8px 10px; font-size: 14px; border: 1px solid #a6a6a6; border-radius: 4px; outline: none; font-family: 'DM Sans', sans-serif; }
        .form-group input:focus { border-color: #B76E79; box-shadow: 0 0 0 3px rgba(183,110,121,0.15); }
        .form-group .error { font-size: 12px; color: #c40000; margin-top: 4px; }
        .remember { display: flex; align-items: center; gap: 6px; margin-top: 12px; }
        .remember input { width: 14px; height: 14px; accent-color: #B76E79; }
        .remember label { font-size: 13px; color: #222222; cursor: pointer; }
        .btn-submit { width: 100%; padding: 10px; background: #B76E79; border: 1px solid #222222; border-radius: 4px; font-size: 14px; font-weight: 500; color: #fff; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s; }
        .btn-submit:hover { background: #222222; }
        .btn-submit:active { background: #7d4f4f; box-shadow: 0 0 0 3px rgba(183,110,121,0.2); }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { font-size: 13px; color: #B76E79; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; color: #222222; }
        .alert { padding: 10px 12px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 4px; font-size: 13px; color: #991b1b; margin-bottom: 16px; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 40px; }
        .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #555; padding: 4px; }
    </style>
</head>
<body>
    <div class="logo">
        <a href="{{ url('/') }}">
            <span style="font-family:'Outfit',sans-serif; color:#B76E79; font-size:32px; font-weight:700; letter-spacing:-0.02em;">Mus<span style="color:#2b2b2b;">Co</span></span>
        </a>
    </div>

    <div class="card">
        <h1>Sign in</h1>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@musco.com">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="pw-wrap">
                    <input type="password" name="password" id="password" required placeholder="Password">
                    <button type="button" class="pw-toggle" onclick="var p=document.getElementById('password'); p.type=p.type==='password'?'text':'password';">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Sign in</button>

            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Keep me signed in</label>
            </div>
        </form>
    </div>

    <div class="back-link">
        <a href="{{ url('/') }}">&larr; Back to {{ config('app.name') }}</a>
    </div>

    <div style="margin-top:24px; font-size:11px; color:#999; text-align:center;">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </div>
</body>
</html>
