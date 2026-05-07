<x-layouts.admin>
    <x-slot name="title">Email Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav', ['active' => 'email'])

    <form action="{{ route('admin.settings.email.update') }}" method="POST" x-data="emailSettings()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- SMTP Configuration -->
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Mail Configuration</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="form-label form-label-required">Mail Driver</label>
                        <select name="mail_driver" required class="form-select" x-model="driver">
                            <option value="smtp">SMTP</option>
                            <option value="sendmail" @selected(($settings['mail_driver'] ?? '') === 'sendmail')>Sendmail</option>
                            <option value="mailgun" @selected(($settings['mail_driver'] ?? '') === 'mailgun')>Mailgun</option>
                            <option value="ses" @selected(($settings['mail_driver'] ?? '') === 'ses')>Amazon SES</option>
                            <option value="postmark" @selected(($settings['mail_driver'] ?? '') === 'postmark')>Postmark</option>
                        </select>
                    </div>

                    <div x-show="driver === 'smtp'">
                        <!-- Quick-fill presets -->
                        <label class="form-label">Quick Fill — Provider Preset</label>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button type="button" @click="preset('gmail')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                Gmail
                            </button>
                            <button type="button" @click="preset('outlook')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                                Outlook / Hotmail
                            </button>
                            <button type="button" @click="preset('yahoo')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors">
                                Yahoo Mail
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="mail_host" x-model="host"
                               value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                               placeholder="smtp.example.com" class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Port</label>
                            <input type="number" name="mail_port" x-model="port"
                                   value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Encryption</label>
                            <select name="mail_encryption" class="form-select" x-model="encryption">
                                <option value="">None</option>
                                <option value="tls" @selected(($settings['mail_encryption'] ?? 'tls') === 'tls')>TLS</option>
                                <option value="ssl" @selected(($settings['mail_encryption'] ?? '') === 'ssl')>SSL</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Username</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="form-input" placeholder="you@gmail.com">
                    </div>

                    <div>
                        <label class="form-label">Password / App Password</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="mail_password"
                                   value=""
                                   placeholder="{{ !empty($settings['mail_password']) ? '••••••••••••' : 'Enter password or app password' }}"
                                   class="form-input pr-10">
                            <button type="button" @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-neutral-400 hover:text-neutral-600">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @if(!empty($settings['mail_password']))
                            <p class="text-xs text-neutral-500 mt-1">Password saved. Leave blank to keep current value.</p>
                        @endif

                        <!-- Gmail App Password Guide -->
                        <div x-show="host === 'smtp.gmail.com'" x-cloak
                             class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <div class="flex gap-2">
                                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs font-semibold text-amber-800">Gmail requires an App Password</p>
                                    <p class="text-xs text-amber-700 mt-0.5">
                                        Your regular Gmail password won't work. Generate a 16-character App Password:
                                    </p>
                                    <ol class="text-xs text-amber-700 mt-1.5 space-y-0.5 list-decimal list-inside">
                                        <li>Go to <strong>Google Account → Security</strong></li>
                                        <li>Enable <strong>2-Step Verification</strong> (required)</li>
                                        <li>Search <strong>"App passwords"</strong> in your Google Account</li>
                                        <li>Create one for <em>Mail / Other</em> and paste the 16-char code above</li>
                                    </ol>
                                    <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-amber-800 underline hover:text-amber-900">
                                        Open Google App Passwords
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender Details + Test -->
            <div class="space-y-6">
                <div class="card">
                    <div class="px-5 py-3.5 border-b border-neutral-200">
                        <h2 class="text-sm font-semibold text-neutral-900">Sender Details</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="form-label form-label-required">From Email</label>
                            <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" required placeholder="noreply@example.com" class="form-input">
                        </div>

                        <div>
                            <label class="form-label form-label-required">From Name</label>
                            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" required placeholder="Your Store Name" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Test Email -->
                <div class="card" x-data="testEmail()">
                    <div class="px-5 py-3.5 border-b border-neutral-200">
                        <h2 class="text-sm font-semibold text-neutral-900">Test Connection</h2>
                    </div>
                    <div class="p-5 space-y-3">
                        <p class="text-xs text-neutral-500">Send a test email using the <strong>currently saved</strong> settings to verify your SMTP is working.</p>
                        <div>
                            <label class="form-label">Send test to</label>
                            <input type="email" x-model="testTo" placeholder="you@example.com" class="form-input">
                        </div>
                        <div x-show="testResult" x-cloak>
                            <div :class="testResult.success ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                                 class="border rounded-lg p-3 text-xs" x-text="testResult.message"></div>
                        </div>
                        <button type="button" @click="send()"
                                :disabled="!testTo || sending"
                                class="btn btn-secondary w-full">
                            <svg class="w-4 h-4 mr-2" :class="sending ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="sending ? 'Sending…' : 'Send Test Email'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

    <script>
    function emailSettings() {
        return {
            driver: '{{ $settings['mail_driver'] ?? 'smtp' }}',
            host: '{{ $settings['mail_host'] ?? '' }}',
            port: '{{ $settings['mail_port'] ?? '587' }}',
            encryption: '{{ $settings['mail_encryption'] ?? 'tls' }}',
            showPass: false,

            preset(provider) {
                const presets = {
                    gmail:   { host: 'smtp.gmail.com',         port: '587', encryption: 'tls' },
                    outlook: { host: 'smtp-mail.outlook.com',  port: '587', encryption: 'tls' },
                    yahoo:   { host: 'smtp.mail.yahoo.com',    port: '465', encryption: 'ssl' },
                };
                const p = presets[provider];
                if (p) { this.host = p.host; this.port = p.port; this.encryption = p.encryption; }
            },
        };
    }

    function testEmail() {
        return {
            testTo: '',
            sending: false,
            testResult: null,

            async send() {
                if (!this.testTo) return;
                this.sending = true;
                this.testResult = null;
                try {
                    const res = await fetch('{{ route("admin.settings.email.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ email: this.testTo }),
                    });
                    this.testResult = await res.json();
                } catch (e) {
                    this.testResult = { success: false, message: 'Request failed.' };
                } finally {
                    this.sending = false;
                }
            },
        };
    }
    </script>
</x-layouts.admin>
