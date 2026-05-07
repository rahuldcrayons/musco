<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Maintenance - {{ config('app.name') }}</title>
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
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .card { text-align: center; max-width: 28rem; }
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
        .code { font-size: 3.5rem; font-weight: 700; color: #222; line-height: 1; margin-bottom: 0.5rem; }
        .title { font-size: 1.25rem; font-weight: 600; color: #222; margin-bottom: 0.5rem; }
        .message { font-size: 0.875rem; color: #666; margin-bottom: 2rem; line-height: 1.6; }
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
            background: linear-gradient(to right, #202a40, #222222);
            color: #fff;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn svg { width: 1rem; height: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <span style="font-family:'Outfit',sans-serif; color:#202a40; font-size:32px; font-weight:700; letter-spacing:-0.02em;">Mus<span style="color:#2b2b2b;">Co</span></span>
    </div>
    <div class="content">
        <div class="card">
            <div class="icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="code">503</div>
            <h1 class="title">We'll Be Right Back</h1>
            <p class="message">We're currently performing scheduled maintenance to improve your shopping experience. Please check back in a few minutes.</p>
            <a href="javascript:location.reload()" class="btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Try Again
            </a>
        </div>
    </div>
</body>
</html>
