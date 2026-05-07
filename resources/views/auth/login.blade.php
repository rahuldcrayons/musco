<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign In — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes toastIn { from { opacity:0; transform:translateY(-12px) } to { opacity:1; transform:translateY(0) } }
        .toast-error { animation: toastIn .3s ease-out both; }
        @keyframes authImgZoom { 0%{transform:scale(1)} 100%{transform:scale(1.08)} }
        @keyframes authSlide { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
        .auth-slide { animation: authSlide .35s ease-out both }
    </style>
</head>
<body class="font-sans antialiased bg-neutral-50 min-h-screen flex items-center justify-center p-4" x-data="{
    mode: '{{ $errors->has('full_name') || $errors->has('phone') || $errors->has('terms') || old('_register') || request()->get('mode') === 'register' ? 'register' : 'login' }}'
}">

@if(session('toast_error'))
<div id="auth-toast" style="position:fixed;top:20px;right:16px;z-index:9999;display:flex;align-items:center;gap:12px;background:#dc2626;color:#fff;font-size:14px;font-weight:500;padding:12px 20px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.25);animation:toastIn .3s ease-out both;max-width:calc(100vw - 32px);">
    <svg style="width:20px;height:20px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <span>{{ session('toast_error') }}</span>
    <button onclick="document.getElementById('auth-toast').remove()" style="background:none;border:none;color:#fff;cursor:pointer;opacity:.75;margin-left:4px;padding:2px;">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
<script>setTimeout(()=>{const t=document.getElementById('auth-toast');if(t)t.remove();},5000);</script>
@endif

    <div class="w-full max-w-[900px] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[560px] relative">
        <a href="{{ url('/') }}"
           class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full text-neutral-500 hover:text-[#202a40] hover:bg-neutral-100 transition-colors"
           aria-label="Close and go home">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>

        {{-- LEFT: Form Panel --}}
        <div class="w-full lg:w-[55%] p-6 sm:p-10 flex flex-col">
            <a href="{{ url('/') }}" class="inline-block mb-6">
                <span style="font-size:1.6rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;"><span style="color:#1e2a40;">Trendy</span><span style="color:#5a6e8a;">mus</span></span>
            </a>

            {{-- ===== LOGIN ===== --}}
            <div x-show="mode === 'login'" class="flex-1 auth-slide" :key="mode">
                <h1 class="text-2xl font-bold text-[#222] mb-1">Welcome back</h1>
                <p class="text-sm text-neutral-500 mb-6">Sign in to continue shopping at <a href="{{ url('/') }}" class="font-semibold text-[#1e2a40] hover:underline">Trendymus</a></p>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-3">
                    @csrf
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email address"
                           class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                    @error('email')<p class="text-xs text-red-500">{{ $message }}</p>@enderror

                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password" required placeholder="Password" autocomplete="current-password"
                               class="w-full px-4 py-2.5 pr-10 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="text-xs text-red-500">{{ $message }}</p>@enderror

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-neutral-300 text-[#202a40]">
                            <span class="text-xs text-neutral-500">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-[#202a40] hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors">
                        Sign In
                    </button>
                </form>

                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-neutral-200"></div>
                    <span class="text-[10px] text-neutral-400">or</span>
                    <div class="flex-1 h-px bg-neutral-200"></div>
                </div>
                @if(config('services.google.client_id') || config('services.facebook.client_id'))
                <div class="flex justify-center gap-4">
                    @if(config('services.google.client_id'))
                    <a href="{{ route('social.redirect', 'google') }}" class="w-10 h-10 flex items-center justify-center border border-neutral-200 rounded-full hover:bg-neutral-50 hover:border-neutral-300 transition-all" title="Continue with Google">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    </a>
                    @endif
                    @if(config('services.facebook.client_id'))
                    <a href="{{ route('social.redirect', 'facebook') }}" class="w-10 h-10 flex items-center justify-center border border-neutral-200 rounded-full hover:bg-[#1877F2]/5 hover:border-[#1877F2]/30 transition-all" title="Continue with Facebook">
                        <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                </div>
                @endif

                <p class="text-center text-xs text-neutral-500 mt-5">
                    Don't have an account?
                    <a href="{{ url('/register') }}" class="font-semibold text-[#202a40] hover:underline">Sign up</a>
                </p>
            </div>

            {{-- ===== REGISTER ===== --}}
            <div x-show="mode === 'register'" x-cloak class="flex-1 auth-slide" :key="mode">
                <h1 class="text-2xl font-bold text-[#222] mb-1">Create account</h1>
                <p class="text-sm text-neutral-500 mb-6">Join <a href="{{ url('/') }}" class="font-semibold text-[#1e2a40] hover:underline">Trendymus</a> for exclusive offers</p>

                <form method="POST" action="{{ route('register') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_register" value="1">

                    {{-- Row 1: Name + Phone --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required autocomplete="name" placeholder="Full name"
                                   pattern="[A-Za-z\s]{2,50}" oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'')"
                                   class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                            @error('full_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="Mobile"
                                   pattern="[6-9][0-9]{9}" maxlength="10" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                                   class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Row 2: Email full width --}}
                    <div>
                        <input type="email" name="email" value="{{ old('_register') ? old('email') : '' }}" required autocomplete="email" placeholder="Email address"
                               class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                        @if(old('_register'))@error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror @endif
                    </div>

                    {{-- Row 3: Password + Confirm --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div x-data="{ show: false }">
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Password" minlength="8"
                                       class="w-full px-4 py-2.5 pr-10 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                            @if(old('_register'))@error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror @endif
                        </div>
                        <div x-data="{ show: false }">
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm" minlength="8"
                                       class="w-full px-4 py-2.5 pr-10 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-[#202a40] transition-colors">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#202a40] hover:bg-[#151e30] text-white font-semibold rounded-lg text-sm transition-colors">
                        Create Account
                    </button>
                </form>

                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-neutral-200"></div>
                    <span class="text-[10px] text-neutral-400">or</span>
                    <div class="flex-1 h-px bg-neutral-200"></div>
                </div>
                @if(config('services.google.client_id') || config('services.facebook.client_id'))
                <div class="flex justify-center gap-4">
                    @if(config('services.google.client_id'))
                    <a href="{{ route('social.redirect', 'google') }}" class="w-10 h-10 flex items-center justify-center border border-neutral-200 rounded-full hover:bg-neutral-50 transition-all" title="Continue with Google">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    </a>
                    @endif
                    @if(config('services.facebook.client_id'))
                    <a href="{{ route('social.redirect', 'facebook') }}" class="w-10 h-10 flex items-center justify-center border border-neutral-200 rounded-full hover:bg-[#1877F2]/5 transition-all" title="Continue with Facebook">
                        <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                </div>
                @endif

                <p class="text-[10px] text-neutral-400 text-center mt-4">
                    By creating an account, you agree to <a href="{{ route('terms') }}" class="underline">Terms</a> & <a href="{{ route('privacy') }}" class="underline">Privacy Policy</a>
                </p>

                <p class="text-center text-xs text-neutral-500 mt-3">
                    Already have an account?
                    <a href="{{ url('/login') }}" class="font-semibold text-[#202a40] hover:underline">Sign in</a>
                </p>
            </div>
        </div>

        {{-- RIGHT: Brand Image Panel --}}
        <div class="hidden lg:flex w-[45%] relative overflow-hidden flex-col">
            <a href="{{ url('/') }}"
               class="absolute top-3 right-3 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-white/80 text-neutral-700 hover:bg-white hover:text-[#202a40] transition-colors shadow-md"
               aria-label="Close and go home">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
            @php $authImg = \App\Models\Product::with('primaryImage')->where('is_active', true)->inRandomOrder()->first(); @endphp
            <div class="absolute inset-0">
                <img src="{{ $authImg?->primary_image_url ?? asset('images/placeholder-product.svg') }}" alt="{{ config('app.name') }}"
                     class="w-full h-full object-cover" style="animation: authImgZoom 18s ease-in-out infinite alternate">
                <div class="absolute inset-0 bg-gradient-to-t from-[#222]/90 via-[#222]/40 to-[#202a40]/30"></div>
            </div>
            <div class="relative z-10 flex flex-col items-center justify-between h-full p-8 text-white text-center">
                <span style="font-size:1.45rem;font-weight:700;letter-spacing:-0.02em;line-height:1;font-family:'DM Sans',sans-serif;margin-top:8px;display:inline-block;"><span style="color:#c8d6e5;">Trendy</span><span style="color:#7a9ab8;">mus</span></span>
                <div>
                    <h3 class="text-2xl font-bold mb-2" x-text="mode === 'register' ? 'Join Trendymus' : 'Welcome Back'"></h3>
                    <p class="text-white/60 text-sm leading-relaxed max-w-[220px] mx-auto"
                       x-text="mode === 'register' ? 'Discover exclusive styles & member-only discounts.' : 'Your wishlist, orders & personalized experience awaits.'"></p>
                </div>
                <div class="w-full space-y-3">
                    <div class="flex justify-center gap-4 text-[11px] text-white/50">
                        <span>✦ Free Shipping</span><span>✦ Easy Returns</span><span>✦ Hallmarked</span>
                    </div>
                    <a :href="mode === 'login' ? '{{ url('/register') }}' : '{{ url('/login') }}'"
                       class="block w-full text-center py-2.5 border border-white/25 rounded-full text-xs font-semibold hover:bg-white hover:text-[#202a40] transition-all duration-300"
                       x-text="mode === 'login' ? 'New here? Create Account →' : '← Already a member? Sign In'"></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
