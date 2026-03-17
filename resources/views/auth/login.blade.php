<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white min-h-screen" x-data>

    <!-- Header: Logo only -->
    <div class="border-b border-neutral-200">
        <div class="max-w-[350px] mx-auto px-4 py-4">
            <a href="{{ url('/') }}" class="block text-center">
                <img src="{{ asset('images/jikra-logo.png') }}" alt="{{ config('app.name') }}" class="h-10 mx-auto object-contain">
            </a>
        </div>
    </div>

    <div class="max-w-[350px] mx-auto px-4 pt-6 pb-8"
         x-data="{
            mode: '{{ $errors->has('full_name') || $errors->has('phone') || $errors->has('terms') || old('_register') || request()->get('mode') === 'register' ? 'register' : 'login' }}'
         }">

        <!-- ============================
             LOGIN FORM
             ============================ -->
        <div x-show="mode === 'login'" x-cloak:remove>
            <div class="border border-neutral-300 rounded-lg p-5 mb-4">
                <h1 class="text-[22px] font-normal text-[#111] mb-4">Sign in</h1>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="login_email" class="block text-[13px] font-bold text-[#111] mb-1">Email</label>
                        <input type="email" name="email" id="login_email" value="{{ old('_register') ? '' : old('email') }}" required autocomplete="email"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @error('email') border-red-500 @enderror"
                               placeholder="">
                        @if(!old('_register'))
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="login_password" class="block text-[13px] font-bold text-[#111]">Password</label>
                            <a href="{{ route('password.request') }}" class="text-[13px] text-[#0066c0] hover:text-[#c45500] hover:underline">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" name="password" id="login_password" required autocomplete="current-password"
                                   class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @error('password') border-red-500 @enderror"
                                   placeholder="">
                        </div>
                        @if(!old('_register'))
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full py-[7px] px-4 bg-gradient-to-b from-[#f7dfa5] to-[#f0c14b] border border-[#a88734] rounded text-[13px] font-normal text-[#111] hover:from-[#f5d78e] hover:to-[#eeb933] focus:outline-none focus:ring-2 focus:ring-[#e77600] cursor-pointer">
                        Sign in
                    </button>
                </form>

                <!-- Keep signed in -->
                <div class="flex items-center mt-4">
                    <input type="checkbox" name="remember" id="remember" class="w-[13px] h-[13px] border-neutral-400 rounded focus:ring-[#e77600] text-[#e77600]" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 text-[13px] text-[#111]">Keep me signed in.</label>
                </div>
            </div>

            <!-- Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-neutral-300"></div>
                </div>
                <div class="relative flex justify-center text-[12px]">
                    <span class="bg-white px-3 text-neutral-500">New to {{ config('app.name') }}?</span>
                </div>
            </div>

            <!-- Create account button -->
            <button @click="mode = 'register'"
                    class="w-full py-[7px] px-4 bg-gradient-to-b from-[#f7f8fa] to-[#e7e9ec] border border-[#adb1b8] rounded text-[13px] text-[#111] hover:from-[#e7eaf0] hover:to-[#d9dce1] focus:outline-none focus:ring-2 focus:ring-[#e77600] cursor-pointer">
                Create your {{ config('app.name') }} account
            </button>
        </div>

        <!-- ============================
             REGISTER FORM
             ============================ -->
        <div x-show="mode === 'register'" x-cloak>
            <div class="border border-neutral-300 rounded-lg p-5 mb-4">
                <h1 class="text-[22px] font-normal text-[#111] mb-4">Create account</h1>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_register" value="1">

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-[13px] font-bold text-[#111] mb-1">Your name</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required autocomplete="name"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @error('full_name') border-red-500 @enderror"
                               placeholder="First and last name">
                        @error('full_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-[13px] font-bold text-[#111] mb-1">Mobile number <span class="font-normal text-neutral-500">(optional)</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="tel"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @error('phone') border-red-500 @enderror"
                               placeholder="Mobile number">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="reg_email" class="block text-[13px] font-bold text-[#111] mb-1">Email</label>
                        <input type="email" name="email" id="reg_email" value="{{ old('_register') ? old('email') : '' }}" required autocomplete="email"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @if(old('_register')) @error('email') border-red-500 @enderror @endif">
                        @if(old('_register'))
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="reg_password" class="block text-[13px] font-bold text-[#111] mb-1">Password</label>
                        <input type="password" name="password" id="reg_password" required autocomplete="new-password"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600] @if(old('_register')) @error('password') border-red-500 @enderror @endif"
                               placeholder="At least 8 characters">
                        <p class="mt-1 text-[12px] text-neutral-600"><span class="font-medium">i</span> Passwords must be at least 8 characters.</p>
                        @if(old('_register'))
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-[13px] font-bold text-[#111] mb-1">Re-enter password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                               class="w-full px-3 py-[7px] border border-neutral-400 rounded text-sm text-[#111] focus:outline-none focus:ring-2 focus:ring-[#e77600] focus:border-[#e77600]">
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full py-[7px] px-4 bg-gradient-to-b from-[#f7dfa5] to-[#f0c14b] border border-[#a88734] rounded text-[13px] font-normal text-[#111] hover:from-[#f5d78e] hover:to-[#eeb933] focus:outline-none focus:ring-2 focus:ring-[#e77600] cursor-pointer">
                        Create your {{ config('app.name') }} account
                    </button>
                </form>

                <p class="mt-4 text-[12px] text-[#111] leading-relaxed">
                    By creating an account, you agree to {{ config('app.name') }}'s
                    <a href="{{ route('terms') }}" class="text-[#0066c0] hover:text-[#c45500] hover:underline">Conditions of Use</a>
                    and
                    <a href="{{ route('privacy') }}" class="text-[#0066c0] hover:text-[#c45500] hover:underline">Privacy Notice</a>.
                </p>
            </div>

            <!-- Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-neutral-300"></div>
                </div>
                <div class="relative flex justify-center text-[12px]">
                    <span class="bg-white px-3 text-neutral-500">Already have an account?</span>
                </div>
            </div>

            <!-- Sign in button -->
            <button @click="mode = 'login'"
                    class="w-full py-[7px] px-4 bg-gradient-to-b from-[#f7f8fa] to-[#e7e9ec] border border-[#adb1b8] rounded text-[13px] text-[#111] hover:from-[#e7eaf0] hover:to-[#d9dce1] focus:outline-none focus:ring-2 focus:ring-[#e77600] cursor-pointer">
                Sign in to your account
            </button>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-neutral-200 mt-4">
        <div class="max-w-[500px] mx-auto px-4 py-6 text-center">
            <div class="flex justify-center gap-4 text-[12px] mb-2">
                <a href="{{ route('terms') }}" class="text-[#0066c0] hover:text-[#c45500] hover:underline">Conditions of Use</a>
                <a href="{{ route('privacy') }}" class="text-[#0066c0] hover:text-[#c45500] hover:underline">Privacy Notice</a>
                <a href="{{ url('/help') }}" class="text-[#0066c0] hover:text-[#c45500] hover:underline">Help</a>
            </div>
            <p class="text-[12px] text-neutral-500">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
