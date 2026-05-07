<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#202a40">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Trendymus">
    <meta name="application-name" content="Trendymus">
    <meta name="msapplication-TileColor" content="#202a40">
    <meta name="format-detection" content="telephone=no">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=7">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=7">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=7">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=7">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=7">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon-192x192.png?v=7">
    <link rel="icon" type="image/png" sizes="512x512" href="/favicon-512x512.png?v=7">

    <!-- SEO Meta Tags -->
    @hasSection('meta')
        @yield('meta')
    @else
        {{ $meta ?? '' }}
    @endif
    @stack('meta')

    <!-- Default OG fallbacks (only if no page-specific meta pushed) -->
    <meta property="og:site_name" content="{{ config('app.name') }}">

    {{-- Canonical is set by each page via @push('meta'). No layout fallback to avoid duplicates. --}}

    <!-- Performance: DNS prefetch + preconnect -->
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    <!-- Fonts (display=swap for fast first paint) -->
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|poppins:400,500,600|inter:400,500&display=swap" rel="stylesheet" />

    <!-- Main Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #nprogress-bar{position:fixed;top:0;left:0;width:0;height:2px;background:linear-gradient(90deg,#202a40,#506282);z-index:99999;transition:width 0.2s ease,opacity 0.4s ease;pointer-events:none;opacity:0}
        #nprogress-bar.active{opacity:1}
    </style>

    <!-- Inline CSRF setup (safety net before JS bundle loads) -->
    <script>
        window.__csrfToken = "{{ csrf_token() }}";
        window.__currencySymbol   = "{{ currency_symbol() }}";
        window.__currencyPosition = "{{ currency_position() }}";
        window.__currencyCode     = "{{ currency_config('code') }}";
        @php
            $__displayCode  = currency_config('code');
            $__exchangeRate = $__displayCode !== 'GBP'
                ? (float) \App\Models\Setting::get('exchange_rate_' . strtolower($__displayCode), 0)
                : 0;
        @endphp
        window.__exchangeRate = {{ $__exchangeRate }};
    </script>

    <style>
        .loader-spinner{width:36px;height:36px;border:3px solid #f0ebe6;border-top-color:#202a40;border-radius:50%;animation:spin .7s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .animate-slide-in{animation:slideIn .3s ease-out}
        @keyframes slideIn{from{opacity:0;transform:translateX(2rem)}to{opacity:1;transform:translateX(0)}}
    </style>

    {{ $styles ?? '' }}

    {{-- AAA Accessibility: prefers-reduced-motion + focus rings --}}
    <style>
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        :focus-visible {
            outline: 2px solid #202a40;
            outline-offset: 2px;
        }
    </style>

    {{-- Google Analytics 4 --}}
    @if(config('services.ga4.measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga4.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.ga4.measurement_id') }}');
    </script>
    @endif

    {{-- Meta Pixel Code --}}
    @if(config('services.facebook.pixel_id'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.facebook.pixel_id') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ config('services.facebook.pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
    {{-- End Meta Pixel Code --}}
</head>
<div id="nprogress-bar"></div>
<body class="font-sans antialiased bg-white text-[#222222]" style="font-family: 'Poppins', sans-serif;" x-data data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
    {{-- Skip Navigation (AAA Accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-white focus:text-neutral-900 focus:rounded-lg focus:shadow-lg focus:ring-2 focus:ring-primary-500 focus:outline-none text-sm font-medium" id="skip-to-main">Skip to main content</a>


    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-[calc(100vw-2rem)]" aria-live="polite">
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div role="alert"
                 class="card px-4 py-3 flex items-center gap-3 shadow-lg border animate-slide-in"
                 :class="{
                     'bg-success-50 border-success-200 text-success-800': toast.type === 'success',
                     'bg-error-50 border-error-200 text-error-800': toast.type === 'error',
                     'bg-warning-50 border-warning-200 text-warning-800': toast.type === 'warning',
                     'bg-info-50 border-info-200 text-info-800': toast.type === 'info'
                 }">
                <span x-text="toast.message"></span>
                <button @click="$store.toast.remove(toast.id)" class="text-current opacity-60 hover:opacity-100" aria-label="Dismiss notification">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Auth Modal — Sliding 2-Panel with OTP Forgot Password -->
    @guest
    <div x-show="$store.authModal.isOpen" x-cloak style="display:none"
         @keydown.escape.window="$store.authModal.close()"
         class="fixed inset-0 z-[99998] flex items-center justify-center p-4">
        <div @click="$store.authModal.close()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[820px] overflow-hidden flex min-h-[480px]" @click.stop
             x-data="{
                email:'', password:'', remember:false, showPw:false,
                name:'', phone:'', password_confirmation:'',
                forgotEmail:'', otpSent:false, otp:['','','','','',''], newPw:'', newPwConfirm:'',
                otpLoading:false, resetLoading:false,
                focusOtp(i) { this.$nextTick(() => this.$refs['otp'+i]?.focus()); },
                handleOtp(i, e) {
                    const v = e.target.value.replace(/\D/g,'');
                    this.otp[i] = v.slice(-1);
                    if (v && i < 5) this.focusOtp(i+1);
                    if (e.inputType === 'deleteContentBackward' && i > 0) this.focusOtp(i-1);
                },
                async sendOtp() {
                    this.otpLoading = true;
                    try {
                        await axios.post('/otp/send-reset', { email: this.forgotEmail });
                        this.otpSent = true;
                        this.$nextTick(() => this.focusOtp(0));
                    } catch(e) { $store.authModal.message = e.response?.data?.message || 'Failed to send OTP'; }
                    this.otpLoading = false;
                },
                async verifyOtp() {
                    this.resetLoading = true;
                    const code = this.otp.join('');
                    try {
                        await axios.post('/otp/reset-password', { email: this.forgotEmail, token: code, password: this.newPw, password_confirmation: this.newPwConfirm });
                        $store.toast.success('Password reset successfully!');
                        $store.authModal.switchMode('login');
                        this.otpSent = false; this.otp = ['','','','','',''];
                    } catch(e) { $store.authModal.message = e.response?.data?.message || 'Invalid OTP or password'; }
                    this.resetLoading = false;
                }
             }">

            {{-- LEFT: Form --}}
            <style>
                .auth-slide-enter{animation:authSlideIn .35s ease-out both}
                @keyframes authSlideIn{0%{opacity:0;transform:translateY(12px)}100%{opacity:1;transform:translateY(0)}}
            </style>
            <div class="w-full lg:w-[55%] p-6 sm:p-8 overflow-y-auto max-h-[85vh] relative"
                 x-effect="$store.authModal.mode; $el.querySelectorAll('.auth-slide-enter').forEach(el=>{el.style.animation='none';el.offsetHeight;el.style.animation=''})">
                <button @click="$store.authModal.close()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Logo + Title --}}
                <div class="text-center mb-5">
                    <div class="flex justify-center mb-3"><span style="font-size:1.4rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#1e2a40;">Trendy</span><span style="color:#5a6e8a;">mus</span></span></div>
                    <h2 class="text-lg font-bold text-[#222]"
                        x-text="$store.authModal.mode==='login'?'Welcome Back':$store.authModal.mode==='register'?'Create Account':'Reset Password'"></h2>
                </div>

                {{-- Error --}}
                <template x-if="$store.authModal.message">
                    <div class="mb-3 px-3 py-2 bg-red-50 border border-red-100 rounded-lg text-xs text-red-600" x-text="$store.authModal.message"></div>
                </template>

                {{-- ===== LOGIN ===== --}}
                <div x-show="$store.authModal.mode==='login'" class="space-y-3 auth-slide-enter">
                    <form @submit.prevent="$store.authModal.login(email, password, remember)" class="space-y-3">
                        <input type="email" x-model="email" required placeholder="Email address" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        <div class="relative">
                            <input :type="showPw?'text':'password'" x-model="password" required placeholder="Password" class="w-full px-4 py-2.5 pr-10 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                            <button type="button" @click="showPw=!showPw" class="absolute inset-y-0 right-3 flex items-center text-neutral-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" x-model="remember" class="w-3.5 h-3.5 rounded border-neutral-300 text-[#202a40] focus:ring-0"><span class="text-xs text-neutral-500">Remember me</span></label>
                            <button type="button" @click="$store.authModal.switchMode('forgot');otpSent=false;otp=['','','','','','']" class="text-xs text-[#202a40] hover:underline">Forgot password?</button>
                        </div>
                        <button type="submit" :disabled="$store.authModal.isLoading" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors disabled:opacity-50">
                            <template x-if="!$store.authModal.isLoading"><span>Sign In</span></template>
                            <template x-if="$store.authModal.isLoading"><span class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Signing in...</span></template>
                        </button>
                    </form>
                    <p class="text-center text-xs text-neutral-500">Don't have an account? <button type="button" @click="$store.authModal.switchMode('register')" class="font-semibold text-[#202a40] hover:underline">Sign up</button></p>
                </div>

                {{-- ===== REGISTER ===== --}}
                <div x-show="$store.authModal.mode==='register'" x-cloak class="space-y-3 auth-slide-enter">
                    <form @submit.prevent="$store.authModal.register(name, email, password, password_confirmation)" class="space-y-3">
                        {{-- Row 1: Name + Phone --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" x-model="name" required placeholder="Full name" autocomplete="name"
                                   pattern="[A-Za-z\s]{2,50}" oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'')"
                                   class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                            <input type="tel" x-model="phone" placeholder="Mobile" autocomplete="tel"
                                   pattern="[6-9][0-9]{9}" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                                   class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        </div>
                        {{-- Row 2: Email --}}
                        <input type="email" x-model="email" required placeholder="Email address" autocomplete="email" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        {{-- Row 3: Password + Confirm --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="password" x-model="password" required placeholder="Password" autocomplete="new-password" minlength="8" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                            <input type="password" x-model="password_confirmation" required placeholder="Confirm" autocomplete="new-password" minlength="8" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        </div>
                        <template x-if="$store.authModal.errors.full_name||$store.authModal.errors.email||$store.authModal.errors.password">
                            <div class="text-xs text-red-500 space-y-0.5">
                                <template x-if="$store.authModal.errors.full_name"><p x-text="$store.authModal.errors.full_name[0]"></p></template>
                                <template x-if="$store.authModal.errors.email"><p x-text="$store.authModal.errors.email[0]"></p></template>
                                <template x-if="$store.authModal.errors.password"><p x-text="$store.authModal.errors.password[0]"></p></template>
                            </div>
                        </template>
                        <button type="submit" :disabled="$store.authModal.isLoading" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors disabled:opacity-50">
                            <template x-if="!$store.authModal.isLoading"><span>Create Account</span></template>
                            <template x-if="$store.authModal.isLoading"><span class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Creating...</span></template>
                        </button>
                    </form>
                    <p class="text-center text-xs text-neutral-500">Already have an account? <button type="button" @click="$store.authModal.switchMode('login')" class="font-semibold text-[#202a40] hover:underline">Sign in</button></p>
                </div>

                {{-- ===== FORGOT PASSWORD (Email → OTP → New Password) ===== --}}
                <div x-show="$store.authModal.mode==='forgot'" x-cloak class="space-y-4 auth-slide-enter">
                    {{-- Step 1: Enter email --}}
                    <div x-show="!otpSent" class="space-y-3">
                        <p class="text-xs text-neutral-500 text-center">Enter your email and we'll send a 6-digit code</p>
                        <input type="email" x-model="forgotEmail" required placeholder="Email address" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        <button @click="sendOtp()" :disabled="otpLoading" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors disabled:opacity-50">
                            <template x-if="!otpLoading"><span>Send OTP</span></template>
                            <template x-if="otpLoading"><span class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Sending...</span></template>
                        </button>
                    </div>

                    {{-- Step 2: Enter OTP + New Password --}}
                    <div x-show="otpSent" class="space-y-3">
                        <p class="text-xs text-neutral-500 text-center">Enter the 6-digit code sent to <span class="font-semibold text-[#222]" x-text="forgotEmail"></span></p>
                        {{-- OTP Boxes --}}
                        <div class="flex justify-center gap-2">
                            <template x-for="(d, i) in otp" :key="i">
                                <input :x-ref="'otp'+i" type="text" inputmode="numeric" maxlength="1"
                                       :value="otp[i]" @input="handleOtp(i, $event)" @keydown.backspace="if(!otp[i] && i>0) focusOtp(i-1)"
                                       @paste.prevent="const t=$event.clipboardData.getData('text').replace(/\D/g,'').slice(0,6); for(let j=0;j<6;j++) otp[j]=t[j]||''; focusOtp(Math.min(t.length,5))"
                                       class="w-10 h-12 text-center text-lg font-bold bg-neutral-50 border border-neutral-200 rounded-lg focus:outline-none focus:border-[#202a40] transition-colors">
                            </template>
                        </div>
                        <input type="password" x-model="newPw" required placeholder="New password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        <input type="password" x-model="newPwConfirm" required placeholder="Confirm new password" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40]">
                        <button @click="verifyOtp()" :disabled="resetLoading || otp.join('').length<6" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors disabled:opacity-50">
                            <template x-if="!resetLoading"><span>Reset Password</span></template>
                            <template x-if="resetLoading"><span class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Resetting...</span></template>
                        </button>
                        <button @click="sendOtp()" class="w-full text-xs text-[#202a40] hover:underline">Resend OTP</button>
                    </div>
                    <p class="text-center text-xs text-neutral-500"><button type="button" @click="$store.authModal.switchMode('login');otpSent=false" class="font-semibold text-[#202a40] hover:underline">← Back to Sign In</button></p>
                </div>

                {{-- T&C --}}
                <p class="text-[10px] text-neutral-400 text-center mt-4">By continuing, you agree to <a href="{{ route('terms') }}" class="underline">Terms</a> & <a href="{{ route('privacy') }}" class="underline">Privacy</a></p>
            </div>

            {{-- RIGHT: Image Panel (desktop) --}}
            <div class="hidden lg:flex w-[45%] relative overflow-hidden flex-col">
                {{-- Jewelry image background --}}
                @php $authImg = App\Models\Product::with('primaryImage')->where('is_active',true)->inRandomOrder()->first(); @endphp
                <div class="absolute inset-0">
                    <img src="{{ $authImg?->primary_image_url ?? asset('images/placeholder-product.svg') }}" alt="Trendymus Jewellery"
                         class="w-full h-full object-cover" style="animation: authImgZoom 20s ease-in-out infinite alternate">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#222]/90 via-[#222]/40 to-[#202a40]/30"></div>
                </div>
                {{-- Content over image --}}
                <div class="relative z-10 flex flex-col items-center justify-between h-full p-8 text-white text-center">
                <span style="font-size:1.35rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;margin-top:8px;"><span style="color:#c8d6e5;">Trendy</span><span style="color:#7a9ab8;">mus</span></span>
                    <div style="animation: authFadeUp .6s ease-out both; animation-delay:.2s">
                        <h3 class="text-2xl font-bold mb-2" style="font-family:'Playfair Display',serif"
                            x-text="$store.authModal.mode==='register'?'Join Trendymus':$store.authModal.mode==='forgot'?'Reset Password':'Welcome Back'"></h3>
                        <p class="text-white/60 text-sm leading-relaxed max-w-[220px] mx-auto"
                           x-text="$store.authModal.mode==='register'?'Exclusive jewellery & member discounts await you.':$store.authModal.mode==='forgot'?'We\'ll help you get back into your account.':'Your wishlist, orders & personalized experience.'"></p>
                    </div>
                    <div class="w-full space-y-3">
                        <div class="flex justify-center gap-6 text-[11px] text-white/50">
                            <span>✦ Free Shipping</span><span>✦ Easy Returns</span><span>✦ Hallmarked</span>
                        </div>
                        <button @click="$store.authModal.switchMode($store.authModal.mode==='login'?'register':'login')"
                                class="w-full py-2.5 border border-white/25 rounded-full text-xs font-semibold hover:bg-white hover:text-[#202a40] transition-all duration-300"
                                x-text="$store.authModal.mode==='login'?'New here? Create Account →':'Already a member? Sign In ←'"></button>
                    </div>
                </div>
                <style>
                    @keyframes authImgZoom{0%{transform:scale(1)}100%{transform:scale(1.1)}}
                    @keyframes authFadeUp{0%{opacity:0;transform:translateY(20px)}100%{opacity:1;transform:translateY(0)}}
                </style>
            </div>
        </div>
    </div>
    @endguest

    <!-- Skip to main content (AAA Accessibility) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-[#202a40] focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:text-sm focus:font-medium focus:shadow-lg">
        Skip to main content
    </a>

    <!-- Header -->
    @include('partials.header')

    <!-- Mobile Navigation -->
    @include('partials.mobile-nav')

    <!-- Notify Stock Listener -->
    <div x-data @notify-stock.window="$store.toast.success('We\'ll notify you when this item is back in stock!')"></div>

    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Back to Top — rendered inside WhatsApp stack (see whatsapp-button.blade.php) -->

    <!-- Footer -->
    @include('partials.footer')

    <!-- Mobile Bottom Navigation -->
    @include('partials.mobile-bottom-nav')

    <!-- Quick View Modal -->
    <div x-data="quickViewModal()"
         x-show="open" x-cloak
         @quick-view.window="show($event.detail.productId)"
         @keydown.escape.window="close()"
         class="fixed inset-0 z-60 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="close()"
             class="absolute inset-0 bg-black/50"></div>

        {{-- Modal Content --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[85vh] overflow-hidden"
             @click.stop>

            {{-- Close --}}
            <button @click="close()" aria-label="Close quick view"
                    class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-neutral-600 hover:text-neutral-800 rounded-full hover:bg-neutral-100 transition-colors z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Loading State --}}
            <div x-show="loading" class="flex items-center justify-center py-20">
                <svg class="w-8 h-8 animate-spin text-[#202a40]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            {{-- Product Content --}}
            <div x-show="!loading && product" class="overflow-y-auto max-h-[85vh]">
                <div class="grid grid-cols-1 sm:grid-cols-2">
                    {{-- Image Gallery --}}
                    <div class="relative bg-neutral-50 aspect-square sm:aspect-auto sm:min-h-[400px]">
                        <img :src="activeImage" :alt="product?.name" class="w-full h-full object-cover">

                        {{-- Image thumbnails --}}
                        <template x-if="product?.images?.length > 1">
                            <div class="absolute bottom-3 left-3 right-3 flex gap-1.5 justify-center">
                                <template x-for="(img, i) in product.images.slice(0, 5)" :key="i">
                                    <button @click="activeImageIndex = i"
                                            class="w-10 h-10 rounded border-2 overflow-hidden bg-white transition-colors"
                                            :class="activeImageIndex === i ? 'border-[#202a40]' : 'border-white/80'">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Details --}}
                    <div class="p-5 flex flex-col">
                        {{-- Brand --}}
                        <p x-show="product?.brand" x-text="product?.brand"
                           class="text-[11px] text-neutral-600 uppercase tracking-wide mb-1"></p>

                        {{-- Name --}}
                        <h2 x-text="product?.name"
                            class="text-lg font-semibold text-neutral-900 leading-snug mb-2"></h2>

                        {{-- Rating --}}
                        <template x-if="product?.rating > 0">
                            <div class="flex items-center gap-1.5 mb-3">
                                <span class="inline-flex items-center gap-0.5 bg-success-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-sm">
                                    <span x-text="product?.rating?.toFixed(1)"></span>
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </span>
                                <span class="text-xs text-neutral-600" x-text="'(' + product?.review_count + ' reviews)'"></span>
                            </div>
                        </template>

                        {{-- Price --}}
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-xl font-bold text-neutral-900" x-text="formatCurrency(product?.price)"></span>
                            <template x-if="product?.price < product?.mrp">
                                <span class="text-sm text-neutral-600 line-through" x-text="formatCurrency(product?.mrp)"></span>
                            </template>
                            <template x-if="product?.discount_percentage > 0">
                                <span class="text-sm font-semibold text-success-600" x-text="Math.round(product?.discount_percentage) + '% off'"></span>
                            </template>
                        </div>

                        {{-- Description --}}
                        <p x-show="product?.short_description" x-text="product?.short_description"
                           class="text-sm text-neutral-600 leading-relaxed mb-4 line-clamp-3"></p>

                        {{-- Stock Status --}}
                        <div class="mb-4">
                            <span x-show="product?.in_stock"
                                  class="inline-flex items-center gap-1 text-xs font-medium text-success-700 bg-success-50 px-2 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                In Stock
                            </span>
                            <span x-show="!product?.in_stock"
                                  class="inline-flex items-center text-xs font-medium text-error-700 bg-error-50 px-2 py-1 rounded-full">
                                Out of Stock
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-auto space-y-2">
                            <template x-if="product?.in_stock">
                                <button @click="$store.cart.add(product.id); close()"
                                        class="w-full py-1.5 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-semibold rounded-lg text-sm transition-colors">
                                    Add to Bag
                                </button>
                            </template>
                            <a :href="product?.url"
                               class="block w-full py-1.5 text-center text-sm font-medium text-[#202a40] rounded-lg hover:bg-[#506282]/5 transition-colors">
                                View Full Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchBar() {
            return {
                query: '',
                results: [],
                loading: false,
                showResults: false,
                listening: false,
                recognition: null,
                currentPlaceholder: '',
                placeholders: [
                    'Search for Diamond Rings...',
                    'Search for Gold Necklaces...',
                    'Search for Silver Earrings...',
                    'Search for Bracelets...',
                    'Search for Mangalsutra...',
                    'Search for Pendants...',
                    'Search for Anklets...',
                    'Search for Bangles...',
                    'Search for Hair Pins...',
                    'Search for Nose Rings...',
                ],
                placeholderIndex: 0,
                charIndex: 0,
                isDeleting: false,
                typewriterTimer: null,
                typewriterActive: true,

                init() {
                    this.startTypewriter();
                    // Setup Speech Recognition
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (SpeechRecognition) {
                        this.recognition = new SpeechRecognition();
                        this.recognition.lang = 'en-IN';
                        this.recognition.continuous = false;
                        this.recognition.interimResults = false;
                        this.recognition.onresult = (event) => {
                            const transcript = event.results[0][0].transcript;
                            this.query = transcript;
                            this.listening = false;
                            this.fetchSuggestions();
                            // Auto-submit after voice input
                            this.$nextTick(() => {
                                this.$refs.searchInput.closest('form').submit();
                            });
                        };
                        this.recognition.onerror = () => {
                            this.listening = false;
                        };
                        this.recognition.onend = () => {
                            this.listening = false;
                        };
                    }
                },

                startTypewriter() {
                    this.typewriterActive = true;
                    this.charIndex = 0;
                    this.isDeleting = false;
                    this.currentPlaceholder = '';
                    this.typewrite();
                },

                stopTypewriter() {
                    this.typewriterActive = false;
                    if (this.typewriterTimer) {
                        clearTimeout(this.typewriterTimer);
                        this.typewriterTimer = null;
                    }
                    this.currentPlaceholder = 'Search products...';
                },

                typewrite() {
                    if (!this.typewriterActive) return;
                    const current = this.placeholders[this.placeholderIndex];

                    if (!this.isDeleting) {
                        this.currentPlaceholder = current.substring(0, this.charIndex + 1);
                        this.charIndex++;
                        if (this.charIndex >= current.length) {
                            // Pause at full text, then start deleting
                            this.typewriterTimer = setTimeout(() => {
                                this.isDeleting = true;
                                this.typewrite();
                            }, 2000);
                            return;
                        }
                        this.typewriterTimer = setTimeout(() => this.typewrite(), 80);
                    } else {
                        this.currentPlaceholder = current.substring(0, this.charIndex - 1);
                        this.charIndex--;
                        if (this.charIndex <= 0) {
                            this.isDeleting = false;
                            this.placeholderIndex = (this.placeholderIndex + 1) % this.placeholders.length;
                            this.typewriterTimer = setTimeout(() => this.typewrite(), 400);
                            return;
                        }
                        this.typewriterTimer = setTimeout(() => this.typewrite(), 40);
                    }
                },

                async fetchSuggestions() {
                    if (this.query.length < 1) {
                        this.results = [];
                        this.showResults = false;
                        return;
                    }
                    this.loading = true;
                    this.showResults = true;
                    try {
                        const response = await axios.get('/search/suggestions', { params: { q: this.query } });
                        this.results = response.data.results || response.data || [];
                    } catch (e) {
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                toggleMic() {
                    if (!this.recognition) {
                        alert('Voice search is not supported in your browser. Please use Chrome or Edge.');
                        return;
                    }
                    if (this.listening) {
                        this.recognition.stop();
                        this.listening = false;
                    } else {
                        this.stopTypewriter();
                        this.query = '';
                        this.recognition.start();
                        this.listening = true;
                    }
                },

                destroy() {
                    this.stopTypewriter();
                    if (this.recognition && this.listening) {
                        this.recognition.stop();
                    }
                }
            };
        }

        function quickViewModal() {
            return {
                open: false,
                loading: false,
                product: null,
                activeImageIndex: 0,

                get activeImage() {
                    if (!this.product?.images?.length) return this.product?.primary_image || '';
                    return this.product.images[this.activeImageIndex] || this.product.primary_image;
                },

                async show(productId) {
                    this.open = true;
                    this.loading = true;
                    this.product = null;
                    this.activeImageIndex = 0;
                    document.body.style.overflow = 'hidden';

                    try {
                        const response = await axios.get(`/product/${productId}/quick-view`);
                        this.product = response.data;
                    } catch (error) {
                        console.error('Quick view failed:', error);
                        Alpine.store('toast').error('Could not load product details');
                        this.close();
                    } finally {
                        this.loading = false;
                    }
                },

                close() {
                    this.open = false;
                    document.body.style.overflow = '';
                }
            };
        }
    </script>

    <!-- Cart Sidebar Drawer -->
    <div x-data x-show="$store.cart.isOpen" x-cloak
         @keydown.escape.window="$store.cart.close()"
         class="fixed inset-0 z-50">

        {{-- Backdrop --}}
        <div x-show="$store.cart.isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.cart.close()"
             class="absolute inset-0 bg-black/40"></div>

        {{-- Drawer --}}
        <div x-show="$store.cart.isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute top-0 right-0 w-full sm:max-w-sm md:max-w-md bg-white shadow-2xl flex flex-col cart-drawer-height">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-4 py-3.5 shrink-0 bg-white" style="box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
                {{-- Back / bag icon --}}
                <button @click="$store.cart.close()" class="w-8 h-8 flex items-center justify-center text-neutral-500 hover:text-neutral-800 rounded-full hover:bg-neutral-100 transition-colors" aria-label="Back">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                {{-- Title --}}
                <div class="flex-1">
                    <h2 class="text-base font-bold text-neutral-900 leading-none">Shopping Bag</h2>
                    <p class="text-[12px] text-neutral-400 mt-0.5" x-text="$store.cart.count + ' item' + ($store.cart.count !== 1 ? 's' : '')"></p>
                </div>
                {{-- Close --}}
                <button @click="$store.cart.close()" class="w-8 h-8 flex items-center justify-center text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100 transition-colors" aria-label="Close cart">
                    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="$store.cart.isLoading && $store.cart.items.length === 0" class="flex-1 flex items-center justify-center">
                <svg class="w-8 h-8 animate-spin text-[#202a40]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            {{-- Empty State --}}
            <div x-show="!$store.cart.isLoading && $store.cart.items.length === 0" class="flex-1 flex flex-col items-center justify-center px-6">
                <div class="w-20 h-20 bg-neutral-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-neutral-700 font-semibold mb-1">Your bag is empty</p>
                <p class="text-sm text-neutral-400 mb-6">Add items to get started</p>
                <button @click="$store.cart.close()" class="px-5 py-2 bg-[#202a40] text-white text-sm font-semibold rounded-full hover:bg-[#2d3a55] transition-colors">
                    Continue Shopping
                </button>
            </div>

            {{-- Items + Recommendations (scrollable) --}}
            <div x-show="$store.cart.items.length > 0" class="flex-1 overflow-y-auto overscroll-contain">

                {{-- Items --}}
                <div class="px-4 py-3 space-y-3">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex gap-3 bg-white rounded-2xl p-3" style="box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
                            {{-- Image --}}
                            <a :href="item.url || '#'" class="shrink-0 rounded-xl overflow-hidden border border-neutral-100 bg-neutral-50" style="width:72px;height:72px;min-width:72px;">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                            </a>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <a :href="item.url || '#'" class="text-[13px] font-semibold text-neutral-900 line-clamp-2 hover:text-[#202a40] transition-colors leading-snug" x-text="item.name"></a>
                                <span class="text-sm font-bold text-neutral-900 mt-0.5 block" x-text="formatCurrency(item.price)"></span>

                                {{-- Quantity pill + Delete --}}
                                <div class="flex items-center justify-between mt-2">
                                    {{-- Pill selector --}}
                                    <div class="flex items-center rounded-full border border-neutral-200 overflow-hidden bg-neutral-50">
                                        <button @click="item.quantity > 1 ? $store.cart.update(item.id, item.quantity - 1) : $store.cart.remove(item.id)"
                                                class="w-8 h-8 flex items-center justify-center text-neutral-600 hover:bg-neutral-200 transition-colors text-base font-medium">−</button>
                                        <span class="px-2 text-sm font-bold text-neutral-900 min-w-[20px] text-center" x-text="item.quantity"></span>
                                        <button @click="$store.cart.update(item.id, item.quantity + 1)"
                                                class="w-8 h-8 flex items-center justify-center text-neutral-600 hover:bg-neutral-200 transition-colors text-base font-medium">+</button>
                                    </div>
                                    {{-- Trash --}}
                                    <button @click="$store.cart.remove(item.id)" class="w-8 h-8 flex items-center justify-center text-neutral-300 hover:text-red-400 hover:bg-red-50 rounded-full transition-colors" aria-label="Remove item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- You May Also Like --}}
                <div x-show="$store.cart.recommendations.length > 0"
                     x-data="{ scrollEl: null }"
                     x-init="scrollEl = $refs.recScroll"
                     class="border-t border-neutral-100 pt-3 pb-2">
                    <div class="flex items-center justify-between px-4 mb-2.5">
                        <h3 class="text-xs font-bold text-neutral-700 uppercase tracking-wide">You May Also Like</h3>
                        <div class="flex gap-1">
                            <button @click="scrollEl.scrollBy({left:-200,behavior:'smooth'})" class="w-6 h-6 flex items-center justify-center rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="scrollEl.scrollBy({left:200,behavior:'smooth'})" class="w-6 h-6 flex items-center justify-center rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                    <div x-ref="recScroll" class="flex gap-2.5 overflow-x-auto px-4 pb-1 scroll-smooth scrollbar-hide" style="-webkit-overflow-scrolling:touch">
                        <template x-for="rec in $store.cart.recommendations.slice(0, 6)" :key="rec.id">
                            <div style="width:100px;min-width:100px;flex-shrink:0;">
                                <a :href="rec.url" class="block" @click="$store.cart.close()">
                                    <div class="relative bg-neutral-50 rounded-xl overflow-hidden mb-1.5 border border-neutral-100" style="width:100px;height:100px;">
                                        <img :src="rec.image" :alt="rec.name" class="w-full h-full object-cover" loading="lazy" style="width:100px;height:100px;object-fit:cover;">
                                        <span x-show="rec.has_discount" class="absolute top-1 left-1 bg-[#F8931D] text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full" x-text="rec.discount_pct + '% Off'"></span>
                                    </div>
                                    <p class="text-[11px] text-neutral-800 font-semibold line-clamp-2 leading-tight mb-0.5" x-text="rec.name"></p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xs font-bold text-neutral-900" x-text="formatCurrency(rec.price)"></span>
                                        <span x-show="rec.has_discount" class="text-[9px] text-neutral-400 line-through" x-text="formatCurrency(rec.mrp)"></span>
                                    </div>
                                </a>
                                <button @click="$store.cart.add(rec.id)"
                                        class="w-full mt-1.5 py-1.5 text-[10px] font-bold text-[#202a40] border border-[#202a40]/25 rounded-lg hover:bg-[#202a40] hover:text-white transition-colors">
                                    Add to Bag
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div x-show="$store.cart.items.length > 0"
                 class="border-t border-neutral-100 px-4 pt-3 bg-white shrink-0"
                 style="padding-bottom: max(16px, env(safe-area-inset-bottom, 16px))">

                {{-- Subtotal row --}}
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm text-neutral-500 font-medium">Subtotal</span>
                    <span class="text-xl font-bold text-neutral-900" x-text="formatCurrency($store.cart.subtotal)"></span>
                </div>
                <p class="text-[11px] text-neutral-400 mb-3">Excl. shipping &amp; taxes</p>

                {{-- Checkout button --}}
                <a href="{{ route('checkout.index') }}" @click="$store.cart.close()"
                   class="flex items-center justify-center gap-2 w-full py-3 bg-[#202a40] hover:bg-[#2d3a55] text-white font-bold rounded-xl text-sm text-center transition-colors shadow-md mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Checkout Securely
                </a>
                <a href="{{ route('cart.index') }}" @click="$store.cart.close()"
                   class="block w-full py-2 text-center text-xs font-semibold text-neutral-400 hover:text-neutral-600 transition-colors">
                    View full bag
                </a>
            </div>
        </div>
    </div>

    <!-- PWA Install Prompt (compact, bottom-left) -->
    <div x-data="pwaInstall()" x-show="showPrompt" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-20 lg:bottom-4 left-3 z-50 bg-white rounded-lg shadow-lg border border-[#efefef] px-3 py-2.5 w-56">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded bg-[#202a40] flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-semibold text-[#222222] leading-tight">Add Trendymus to Home Screen</p>
            </div>
            <button @click="dismiss()" class="shrink-0 text-[#555555] hover:text-[#222222]" aria-label="Dismiss">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex gap-1.5 mt-2">
            <button @click="dismiss()" class="flex-1 py-1 text-[10px] font-medium text-[#555555] bg-[#f7f7f7] rounded hover:bg-[#efefef] transition-colors">Later</button>
            <button @click="install()" class="flex-1 py-1 text-[10px] font-medium text-white bg-[#202a40] rounded transition-colors">Install</button>
        </div>
    </div>

    {{ $scripts ?? '' }}

    {{-- Global Search Modal (Ctrl+K / Cmd+K) --}}
    <div x-data="{
            open: false,
            q: '',
            results: [],
            loading: false,
            recent: JSON.parse(localStorage.getItem('recentSearches') || '[]'),
            selected: -1,
            show() { this.open = true; this.q = ''; this.results = []; this.selected = -1; document.body.style.overflow = 'hidden'; this.$nextTick(() => this.$refs.sinput?.focus()); },
            hide() { this.open = false; document.body.style.overflow = ''; },
            async search() {
                if (this.q.length < 2) { this.results = []; return; }
                this.loading = true;
                try {
                    const r = await axios.get('/search/suggestions', { params: { q: this.q } });
                    this.results = r.data.results || r.data || [];
                } catch(e) { this.results = []; }
                this.loading = false;
                this.selected = -1;
            },
            go(url, name) {
                let rec = this.recent.filter(r => r !== name);
                rec.unshift(name);
                rec = rec.slice(0, 5);
                localStorage.setItem('recentSearches', JSON.stringify(rec));
                this.hide();
                window.location.href = url;
            },
            onKey(e) {
                const len = this.results.length;
                if (e.key === 'ArrowDown') { e.preventDefault(); this.selected = (this.selected + 1) % len; }
                else if (e.key === 'ArrowUp') { e.preventDefault(); this.selected = (this.selected - 1 + len) % len; }
                else if (e.key === 'Enter' && this.selected >= 0 && this.results[this.selected]) {
                    e.preventDefault(); this.go(this.results[this.selected].url, this.results[this.selected].name);
                } else if (e.key === 'Enter' && this.q.length >= 2) {
                    e.preventDefault(); this.hide(); window.location.href = '/search?q=' + encodeURIComponent(this.q);
                }
            },
            clearRecent() { this.recent = []; localStorage.removeItem('recentSearches'); }
         }"
         @open-search.window="show()"
         @keydown.window="if((e=$event).key==='k' && (e.metaKey||e.ctrlKey)){e.preventDefault(); open ? hide() : show()}"
         @keydown.escape.window="if(open) hide()"
         x-show="open" x-cloak style="display:none"
         class="fixed inset-0 z-[99999]">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="hide()"></div>

        <div class="relative max-w-xl w-full mx-auto mt-[10vh] bg-white rounded-2xl shadow-2xl overflow-hidden" @click.stop>
            {{-- Search input --}}
            <div class="flex items-center gap-3 px-4 py-3 border-b border-[#eee]">
                <svg class="w-5 h-5 text-[#202a40] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-ref="sinput" x-model="q" @input.debounce.250ms="search()" @keydown="onKey($event)"
                       type="text" placeholder="Search products, categories..."
                       class="flex-1 text-sm bg-transparent border-none outline-none placeholder-neutral-400">
                <div class="flex items-center gap-2">
                    <template x-if="loading">
                        <div class="loader-spinner" style="width:18px;height:18px;border-width:2px"></div>
                    </template>
                    <kbd class="hidden sm:inline text-[10px] text-neutral-400 bg-neutral-100 px-1.5 py-0.5 rounded font-mono">ESC</kbd>
                </div>
            </div>

            {{-- Results --}}
            <div class="max-h-[60vh] overflow-y-auto">
                {{-- Recent searches (when empty) --}}
                <template x-if="q.length < 2 && recent.length > 0">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wide">Recent</span>
                            <button @click="clearRecent()" class="text-xs text-neutral-400 hover:text-red-400">Clear</button>
                        </div>
                        <template x-for="term in recent" :key="term">
                            <button @click="q = term; search()" class="flex items-center gap-2 w-full px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-50 rounded-lg text-left">
                                <svg class="w-3.5 h-3.5 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="term"></span>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- Search results --}}
                <template x-if="results.length > 0">
                    <div class="p-2">
                        <template x-for="(item, idx) in results" :key="item.url || idx">
                            <a :href="item.url" @click.prevent="go(item.url, item.name || item.title)"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors"
                               :class="selected === idx ? 'bg-[#FDF2F3]' : 'hover:bg-neutral-50'">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover bg-neutral-100 shrink-0">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                                    </div>
                                </template>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-neutral-800 truncate"
                                       x-html="(item.name||item.title||'').replace(new RegExp('('+q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+')','gi'), '<mark class=&quot;bg-[#FFF3CD] text-[#222] rounded px-0.5 font-bold&quot;>$1</mark>')"></p>
                                    <div class="flex items-center gap-2">
                                        <span x-show="item.price" class="text-xs font-bold text-[#202a40]" x-text="item.price ? window.__currencySymbol+Number(item.price).toLocaleString('en-IN') : ''"></span>
                                        <span class="text-[10px] text-neutral-400 uppercase" x-text="item.type || item.category || ''"></span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-neutral-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </a>
                        </template>
                    </div>
                </template>

                {{-- No results --}}
                <template x-if="q.length >= 2 && !loading && results.length === 0">
                    <div class="p-8 text-center">
                        <p class="text-sm text-neutral-400">No results for "<span x-text="q" class="font-medium text-neutral-600"></span>"</p>
                        <a :href="'/search?q=' + encodeURIComponent(q)" class="inline-block mt-3 text-xs text-[#202a40] font-semibold hover:underline">Search all products →</a>
                    </div>
                </template>

                {{-- Trending searches (when no query and no recent) --}}
                <template x-if="q.length < 2 && recent.length === 0">
                    <div class="p-3">
                        <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wide px-1">Trending</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @php
                                $trending = \App\Models\SearchLog::select('query')
                                    ->selectRaw('COUNT(*) as cnt')
                                    ->where('created_at', '>=', now()->subDays(7))
                                    ->groupBy('query')
                                    ->orderByDesc('cnt')
                                    ->take(8)
                                    ->pluck('query')
                                    ->toArray();
                                if (empty($trending)) {
                                    $trending = ['Rings', 'Gold Necklace', 'Diamond', 'Earrings', 'Bracelet', 'Mangalsutra', 'Silver', 'Pendant'];
                                }
                            @endphp
                            @foreach($trending as $term)
                                <button @click="q = '{{ addslashes($term) }}'; search()"
                                        class="px-3 py-1.5 text-xs font-medium text-neutral-600 bg-neutral-100 hover:bg-[#FDF2F3] hover:text-[#202a40] rounded-full transition-colors">
                                    <svg class="w-3 h-3 inline mr-1 text-neutral-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                                    {{ $term }}
                                </button>
                            @endforeach
                        </div>

                        <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wide px-1 mt-4 block">Popular Categories</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach(\App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->take(6)->get(['id','name','slug']) as $cat)
                                <a href="{{ url('/categories/' . $cat->slug) }}" @click.prevent="hide(); window.location.href='{{ url('/categories/' . $cat->slug) }}'"
                                   class="px-3 py-1.5 text-xs font-medium text-[#202a40] bg-[#FDF2F3] hover:bg-[#202a40] hover:text-white rounded-full transition-colors">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="px-4 py-2 border-t border-[#eee] flex items-center justify-between text-[10px] text-neutral-400">
                <div class="flex items-center gap-3">
                    <span><kbd class="bg-neutral-100 px-1 rounded font-mono">↑↓</kbd> Navigate</span>
                    <span><kbd class="bg-neutral-100 px-1 rounded font-mono">↵</kbd> Open</span>
                    <span><kbd class="bg-neutral-100 px-1 rounded font-mono">ESC</kbd> Close</span>
                </div>
                <span>Ctrl+K</span>
            </div>
        </div>
    </div>

    {{-- Quick-View Popup Modal --}}
    <div x-data="{ open:false, image:'', name:'', url:'', price:'', mrp:'', rating:0, review_count:0, sales_count:0, stock:0, desc:'', id:0 }"
         @open-preview.window="
            image        = $event.detail.image;
            name         = $event.detail.name;
            url          = $event.detail.url;
            price        = $event.detail.price;
            mrp          = $event.detail.mrp || '';
            rating       = parseFloat($event.detail.rating) || 0;
            review_count = $event.detail.review_count || 0;
            sales_count  = $event.detail.sales_count || 0;
            stock        = $event.detail.stock || 0;
            desc         = $event.detail.desc || '';
            id           = $event.detail.id || 0;
            open         = true;
            document.body.style.overflow = 'hidden';
         "
         @keydown.escape.window="if(open){ open=false; document.body.style.overflow=''; }"
         x-show="open" x-cloak style="display:none"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             @click="open=false; document.body.style.overflow=''"></div>

        {{-- Modal card --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden z-10"
             @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Close button --}}
            <button @click="open=false; document.body.style.overflow=''"
                    class="absolute top-3 right-3 z-20 w-8 h-8 bg-white/90 hover:bg-[#f0f0f0] rounded-full flex items-center justify-center text-[#444] shadow transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex flex-col sm:flex-row">
                {{-- Product Image --}}
                <div class="sm:w-52 sm:shrink-0 bg-[#FAF7F5] flex items-center justify-center p-4 min-h-[200px]">
                    <img :src="image" :alt="name"
                         class="w-full max-h-60 sm:max-h-none object-contain"
                         onerror="this.src='{{ asset('images/placeholder-product.svg') }}'">
                </div>

                {{-- Product Details --}}
                <div class="flex flex-col flex-1 p-5 gap-2.5 overflow-y-auto max-h-[80vh]">

                    {{-- Name --}}
                    <h3 class="text-sm font-semibold text-[#222] leading-snug line-clamp-3" x-text="name"></h3>

                    {{-- Stars + review count --}}
                    <div x-show="rating > 0" class="flex items-center gap-1.5">
                        <div class="flex items-center gap-px">
                            <template x-for="s in 5" :key="s">
                                <svg class="w-3.5 h-3.5" :fill="s <= Math.round(rating) ? '#f5a623' : '#e0e0e0'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </template>
                        </div>
                        <span class="text-[11px] font-semibold" style="color:#007185;" x-text="rating"></span>
                        <span class="text-[11px] text-[#999]" x-text="'(' + review_count + ' reviews)'"></span>
                    </div>

                    <hr class="border-[#efefef]">

                    {{-- Price block --}}
                    <div class="flex flex-col gap-0.5">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span class="text-2xl font-bold text-[#222]"
                                  x-text="window.__currencySymbol + Number(price).toLocaleString('en-IN')"></span>
                            <span x-show="mrp && Number(mrp) > Number(price)"
                                  class="text-sm text-[#999] line-through"
                                  x-text="'M.R.P. ' + window.__currencySymbol + Number(mrp).toLocaleString('en-IN')"></span>
                            <span x-show="mrp && Number(mrp) > Number(price)"
                                  class="text-[11px] font-bold px-1.5 py-0.5 rounded text-white"
                                  style="background:#202a40;"
                                  x-text="'-' + Math.round((1 - Number(price)/Number(mrp))*100) + '% OFF'"></span>
                        </div>
                        <span x-show="mrp && Number(mrp) > Number(price)"
                              class="text-xs font-semibold" style="color:#2e9e5b;"
                              x-text="'You save: ' + window.__currencySymbol + (Number(mrp)-Number(price)).toLocaleString('en-IN') + ' (' + Math.round((1-Number(price)/Number(mrp))*100) + '%)'"></span>
                        <p class="text-xs text-[#555]">Inclusive of all taxes</p>
                    </div>

                    {{-- Free delivery + social proof --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        <span x-show="Number(price) >= 499"
                              class="flex items-center gap-1 text-xs font-medium" style="color:#007185;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
                            Free Delivery
                        </span>
                        <span x-show="sales_count >= 10" class="text-xs text-[#555]">
                            <span class="font-semibold" x-text="sales_count >= 1000 ? (sales_count/1000).toFixed(1)+'k+' : sales_count + '+'"></span> bought
                        </span>
                    </div>

                    {{-- Low stock --}}
                    <p x-show="stock > 0 && stock <= 5"
                       class="flex items-center gap-1 text-xs font-semibold" style="color:#c0392b;">
                        <span class="w-2 h-2 rounded-full bg-[#c0392b] animate-pulse inline-block"></span>
                        <span x-text="'Only ' + stock + ' left in stock'"></span>
                    </p>

                    {{-- Short description --}}
                    <p x-show="desc" x-text="desc" class="text-xs text-[#666] leading-relaxed border-t border-[#efefef] pt-2"></p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col gap-2 mt-auto pt-1">
                        <button @click="if(id) Alpine.store('cart').add(id); open=false; document.body.style.overflow='';"
                                class="w-full py-2.5 text-sm font-semibold text-white bg-[#202a40] rounded-full transition-colors shadow-sm">
                            Add to Cart
                        </button>
                        <a :href="url"
                           class="w-full py-2.5 text-sm font-semibold text-[#222] bg-white border border-[#ddd] hover:border-[#202a40] hover:text-[#202a40] rounded-full transition-colors text-center">
                            View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Worker Registration (production only) -->
    <script>
        if ('serviceWorker' in navigator) {
            @if(app()->environment('production'))
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            @else
                // In dev: unregister service workers AND clear all caches to prevent stale files
                navigator.serviceWorker.getRegistrations().then(registrations => {
                    registrations.forEach(r => r.unregister());
                });
                caches.keys().then(keys => keys.forEach(k => caches.delete(k)));
            @endif
        }

        function pwaInstall() {
            return {
                showPrompt: false,
                deferredPrompt: null,
                init() {
                    if (window.matchMedia('(display-mode: standalone)').matches) return;
                    if (sessionStorage.getItem('pwa_dismissed')) return;
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        setTimeout(() => { this.showPrompt = true; }, 30000);
                    });
                },
                async install() {
                    if (!this.deferredPrompt) return;
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    this.deferredPrompt = null;
                    this.showPrompt = false;
                    if (outcome === 'accepted') sessionStorage.setItem('pwa_dismissed', '1');
                },
                dismiss() {
                    this.showPrompt = false;
                    sessionStorage.setItem('pwa_dismissed', '1');
                }
            };
        }
    </script>

    <x-whatsapp-button />
    <x-exit-intent-popup />

    <script>
    (function(){
        var bar = document.getElementById('nprogress-bar');
        var timer, width = 0, raf;

        function start() {
            clearTimeout(timer);
            cancelAnimationFrame(raf);
            width = 0;
            bar.style.width = '0%';
            bar.classList.add('active');
            grow();
        }
        function grow() {
            var target = width < 50 ? width + 8 : width < 80 ? width + 3 : width < 95 ? width + 0.5 : width;
            if (target > 95) target = 95;
            width = target;
            bar.style.width = width + '%';
            if (width < 95) raf = requestAnimationFrame(function(){ timer = setTimeout(grow, 100); });
        }
        function done() {
            cancelAnimationFrame(raf);
            clearTimeout(timer);
            width = 100;
            bar.style.width = '100%';
            setTimeout(function(){ bar.classList.remove('active'); bar.style.width = '0%'; }, 400);
        }

        // Trigger on same-origin link clicks (not #hash, not target=_blank, not AJAX)
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank') return;
            try {
                var url = new URL(href, location.href);
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname && url.search === location.search) return;
            } catch(e) { return; }
            start();
        });

        // Trigger on form submits
        document.addEventListener('submit', function(e) {
            if (e.target.method && e.target.method.toLowerCase() !== 'get') start();
        });

        // Complete when page finishes loading
        window.addEventListener('pageshow', done);
    })();
    </script>
    <script>
    function pcScroll(btn, dir) {
        var grid = btn.closest('.pc-section-header').nextElementSibling;
        while (grid && !grid.classList.contains('pc-glass-grid')) {
            grid = grid.nextElementSibling;
        }
        if (!grid) return;
        var card = grid.firstElementChild;
        var cardW = card ? card.offsetWidth + parseInt(getComputedStyle(grid).gap) : 260;
        var visible = Math.round(grid.offsetWidth / cardW) || 1;
        grid.scrollBy({ left: dir * cardW * visible, behavior: 'smooth' });
    }
    </script>
</body>
</html>
