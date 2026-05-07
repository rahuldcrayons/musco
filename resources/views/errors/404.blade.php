<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Page Not Found – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: #080d18;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Deep space background ── */
        .bg {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 90% 70% at 15% 5%,  rgba(32,42,64,.9) 0%, transparent 55%),
                radial-gradient(ellipse 70% 60% at 85% 95%, rgba(43,43,43,.75) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 50%, rgba(245,166,35,.05) 0%, transparent 65%),
                linear-gradient(160deg, #0b1120 0%, #111927 50%, #080d18 100%);
        }

        /* ── Orbs ── */
        .orb { position: fixed; border-radius: 50%; filter: blur(90px); pointer-events: none; }
        .o1 { width: 420px; height: 420px; background: #202a40; top: -120px; left: -100px; opacity: .25; animation: ob1 13s ease-in-out infinite alternate; }
        .o2 { width: 300px; height: 300px; background: #f5a623;  bottom: -80px;  right: -80px;  opacity: .08; animation: ob2 10s ease-in-out infinite alternate; }
        .o3 { width: 200px; height: 200px; background: #3b5998; top: 55%; left: 55%; opacity: .10; animation: ob1 8s ease-in-out infinite alternate; }
        @keyframes ob1 { to { transform: translate(40px, 30px) scale(1.08); } }
        @keyframes ob2 { to { transform: translate(-30px, -20px) scale(1.12); } }

        /* ── Stars ── */
        .stars { position: fixed; inset: 0; pointer-events: none; }
        .star {
            position: absolute; border-radius: 50%; background: #fff;
            animation: twinkle var(--t,3s) ease-in-out infinite alternate;
            animation-delay: var(--d,0s);
        }
        @keyframes twinkle {
            from { opacity: var(--a,.4); transform: scale(1); }
            to   { opacity: calc(var(--a,.4)*.15); transform: scale(.4); }
        }

        /* ── Glassmorphism card ── */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,.055);
            backdrop-filter: blur(28px) saturate(1.5);
            -webkit-backdrop-filter: blur(28px) saturate(1.5);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 32px;
            padding: 2.75rem 2.25rem 2.25rem;
            width: calc(100% - 2rem);
            max-width: 420px;
            text-align: center;
            box-shadow:
                0 40px 100px rgba(0,0,0,.6),
                inset 0 1px 0 rgba(255,255,255,.10),
                inset 0 0 0 1px rgba(255,255,255,.04);
            animation: cardIn .7s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform: translateY(50px) scale(.95); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* ── Logo ── */
        .logo-wrap {
            margin-bottom: 1.75rem;
            animation: logoPop .6s cubic-bezier(.22,1,.36,1) .1s both;
        }
        @keyframes logoPop {
            from { opacity:0; transform: scale(.8) translateY(-8px); }
            to   { opacity:1; transform: scale(1)  translateY(0); }
        }

        /* ── Big illustrated 404 ── */
        .num-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .1em;
            margin-bottom: .75rem;
            animation: numIn .6s cubic-bezier(.22,1,.36,1) .15s both;
        }
        @keyframes numIn { from { opacity:0; transform: scale(.7); } to { opacity:1; transform: scale(1); } }

        .digit {
            font-size: clamp(5.5rem, 22vw, 8.5rem);
            font-weight: 900;
            line-height: .95;
            letter-spacing: -.04em;
            background: linear-gradient(160deg, #ffffff 20%, rgba(255,255,255,.25) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .digit-mid {
            width: clamp(60px, 16vw, 90px);
            height: clamp(60px, 16vw, 90px);
            background: rgba(245,166,35,.13);
            border: 2px solid rgba(245,166,35,.35);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: boxFloat 3s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(245,166,35,.12), inset 0 1px 0 rgba(245,166,35,.2);
            flex-shrink: 0;
        }
        .digit-mid svg { width: 52%; height: 52%; color: #f5a623; }
        @keyframes boxFloat {
            0%,100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-9px) rotate(2deg); }
        }

        /* ── Smile arrow (Amazon touch) ── */
        .smile {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            margin: 0 auto .9rem;
        }
        .smile-line { flex: 1; max-width: 56px; height: 1px; background: rgba(255,255,255,.12); }
        .smile svg { width: 32px; color: #f5a623; }

        /* ── Text ── */
        .headline {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.5rem;
        }

        /* ── Buttons ── */
        .actions { display: flex; gap: .6rem; justify-content: center; }
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1.25rem; font-size: .8rem; font-weight: 600;
            text-decoration: none; border-radius: 10px; border: none;
            cursor: pointer; font-family: 'Outfit', sans-serif; transition: all .2s;
        }
        .btn svg { width: 14px; height: 14px; }
        .btn-primary { background: #202a40; color: #fff; border: 1px solid rgba(255,255,255,.10); }
        .btn-primary:hover { background: #2d3a55; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(32,42,64,.5); }
        .btn-ghost { background: rgba(255,255,255,.06); color: rgba(255,255,255,.65); border: 1px solid rgba(255,255,255,.09); }
        .btn-ghost:hover { background: rgba(255,255,255,.11); color: #fff; }

    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="orb o1"></div>
    <div class="orb o2"></div>
    <div class="orb o3"></div>
    <div class="stars" id="stars"></div>

    <div class="card">

        <!-- Logo -->
        <div class="logo-wrap">
            <a href="{{ url('/') }}" style="text-decoration:none;">
                <span style="font-size:1.5rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'Outfit',sans-serif;"><span style="color:#c8d6e5;">Trendy</span><span style="color:#7a9ab8;">mus</span></span>
            </a>
        </div>

        <!-- 404 with floating box as the 0 -->
        <div class="num-row">
            <span class="digit">4</span>
            <div class="digit-mid">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="digit">4</span>
        </div>

        <!-- Smile / arrow divider -->
        <div class="smile">
            <div class="smile-line"></div>
            <svg viewBox="0 0 80 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4 C20 16, 60 16, 76 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                <path d="M70 2 L76 4 L72 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="smile-line"></div>
        </div>

        <h1 class="headline">Oops! This page is missing.</h1>

        <!-- Actions -->
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Go to Homepage
            </a>
            <button onclick="history.back()" class="btn btn-ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </button>
        </div>

    </div>

    <script>
        const s = document.getElementById('stars');
        for (let i = 0; i < 100; i++) {
            const el = document.createElement('div');
            el.className = 'star';
            const sz = Math.random() < .12 ? 3 : Math.random() < .4 ? 2 : 1.5;
            el.style.cssText = `left:${(Math.random()*100).toFixed(1)}%;top:${(Math.random()*100).toFixed(1)}%;
                width:${sz}px;height:${sz}px;
                --t:${(Math.random()*4+1.5).toFixed(1)}s;
                --d:${(Math.random()*6).toFixed(1)}s;
                --a:${(Math.random()*.55+.08).toFixed(2)};`;
            s.appendChild(el);
        }
    </script>
</body>
</html>
