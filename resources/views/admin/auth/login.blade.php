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
        .form-group input:focus { border-color: #202a40; box-shadow: 0 0 0 3px rgba(183,110,121,0.15); }
        .form-group .error { font-size: 12px; color: #c40000; margin-top: 4px; }
        .remember { display: flex; align-items: center; gap: 6px; margin-top: 12px; }
        .remember input { width: 14px; height: 14px; accent-color: #202a40; }
        .remember label { font-size: 13px; color: #222222; cursor: pointer; }
        .btn-submit { width: 100%; padding: 10px; background: #202a40; border: 1px solid #222222; border-radius: 4px; font-size: 14px; font-weight: 500; color: #fff; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s; }
        .btn-submit:hover { background: #222222; }
        .btn-submit:active { background: #7d4f4f; box-shadow: 0 0 0 3px rgba(183,110,121,0.2); }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { font-size: 13px; color: #202a40; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; color: #222222; }
        .alert { padding: 10px 12px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 4px; font-size: 13px; color: #991b1b; margin-bottom: 16px; }
        @keyframes toastIn { from { opacity:0; transform:translate(-50%,-12px) } to { opacity:1; transform:translate(-50%,0) } }
        .toast-popup { position:fixed; top:20px; right:16px; z-index:9999; display:flex; align-items:center; gap:10px; background:#dc2626; color:#fff; font-size:13px; font-weight:500; padding:12px 18px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.2); animation:toastIn .3s ease-out both; max-width:90vw; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 40px; }
        .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #555; padding: 4px; }
    </style>
</head>
<body>
    @if(session('toast_error'))
    <div class="toast-popup" id="admin-toast">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <span>{{ session('toast_error') }}</span>
        <button onclick="document.getElementById('admin-toast').remove()" style="background:none;border:none;color:#fff;cursor:pointer;opacity:.7;margin-left:6px;">✕</button>
    </div>
    <script>setTimeout(()=>{ const t=document.getElementById('admin-toast'); if(t) t.remove(); }, 5000);</script>
    @endif

    <div class="logo">
        <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;">
            <span style="font-size:1.6rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#1e2a40;">Trendy</span><span style="color:#5a6e8a;">mus</span></span>
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
                <input type="email" name="email" id="email" value="{{ old('email', session('_old_input.email', '')) }}" required autofocus placeholder="admin@trendymus.com">
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
        <a href="{{ url('/') }}">&larr; Back to Store</a>
    </div>

    <div style="margin-top:24px; text-align:center; opacity:0.4;">
        <span style="font-size:1rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#1e2a40;">Trendy</span><span style="color:#5a6e8a;">mus</span></span>
    </div>
</body>
</html>
