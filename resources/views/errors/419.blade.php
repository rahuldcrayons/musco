<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Page Expired - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 1.5rem;
            text-align: center;
        }
        .header img { height: 2.5rem; object-fit: contain; }
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .card {
            text-align: center;
            max-width: 28rem;
        }
        .icon-wrap {
            width: 6rem;
            height: 6rem;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #202a40 0%, #222222 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-wrap svg { width: 3rem; height: 3rem; color: #fff; }
        .code {
            font-size: 3.5rem;
            font-weight: 700;
            color: #222;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 0.5rem;
        }
        .message {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(to right, #202a40, #222222);
            color: #fff;
            box-shadow: 0 4px 12px rgba(248, 147, 29, 0.25);
        }
        .btn-primary:hover { box-shadow: 0 4px 16px rgba(248, 147, 29, 0.4); transform: translateY(-1px); }
        .btn-outline {
            background: #fff;
            color: #555;
            border: 1px solid #ddd;
        }
        .btn-outline:hover { border-color: #202a40; color: #202a40; }
        .btn svg { width: 1rem; height: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ url('/') }}">
            <span style="font-family:'Outfit',sans-serif; color:#202a40; font-size:32px; font-weight:700; letter-spacing:-0.02em;">Mus<span style="color:#2b2b2b;">Co</span></span>
        </a>
    </div>
    <div class="content">
        <div class="card">
            <div class="icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="code">419</div>
            <h1 class="title">Session Expired</h1>
            <p class="message">Your form session expired. Redirecting you back in <strong id="count">3</strong>s — please resubmit once the page reloads.</p>
            <div class="actions">
                <a id="back-btn" href="javascript:history.back()" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Go Back & Try Again
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Go Home
                </a>
            </div>
            <script>
                // Auto-redirect back after 3 seconds
                let c = 3;
                const el = document.getElementById('count');
                const timer = setInterval(() => {
                    c--;
                    if (el) el.textContent = c;
                    if (c <= 0) {
                        clearInterval(timer);
                        if (document.referrer) {
                            window.location.href = document.referrer;
                        } else {
                            history.back();
                        }
                    }
                }, 1000);
                // Update back button to use referrer if available
                const btn = document.getElementById('back-btn');
                if (btn && document.referrer) {
                    btn.href = document.referrer;
                }
            </script>
        </div>
    </div>
</body>
</html>
