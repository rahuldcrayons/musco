<x-layouts.admin>
    <x-slot name="title">General Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Settings</h1>
        <p class="text-neutral-600">Manage your store configuration</p>
    </div>

    <!-- Settings Navigation -->
    @include('admin.settings.partials.nav', ['active' => 'general'])

    <form action="{{ route('admin.settings.general.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Store Information -->
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Store Information</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="form-label form-label-required">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required class="form-input">
                        @error('site_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="form-input">
                        @error('site_tagline') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label form-label-required">Email Address</label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}" required class="form-input">
                        @error('site_email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="site_phone" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}" class="form-input">
                        @error('site_phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Address</label>
                        <textarea name="site_address" rows="3" class="form-textarea">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                        @error('site_address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div class="card">
                <div class="px-5 py-3.5 border-b border-neutral-200">
                    <h2 class="text-sm font-semibold text-neutral-900">Regional Settings</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="form-label form-label-required">Timezone</label>
                        <select name="timezone" required class="form-select">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" @selected(($settings['timezone'] ?? 'UTC') === $tz)>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label form-label-required">Date Format</label>
                        <select name="date_format" required class="form-select">
                            <option value="M d, Y" @selected(($settings['date_format'] ?? 'M d, Y') === 'M d, Y')>{{ now()->format('M d, Y') }}</option>
                            <option value="d/m/Y" @selected(($settings['date_format'] ?? '') === 'd/m/Y')>{{ now()->format('d/m/Y') }}</option>
                            <option value="m/d/Y" @selected(($settings['date_format'] ?? '') === 'm/d/Y')>{{ now()->format('m/d/Y') }}</option>
                            <option value="Y-m-d" @selected(($settings['date_format'] ?? '') === 'Y-m-d')>{{ now()->format('Y-m-d') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label form-label-required">Currency</label>
                        <select name="currency" id="currency-select" required class="form-select">
                            <option value="USD" @selected(($settings['currency'] ?? 'INR') === 'USD')>USD - US Dollar</option>
                            <option value="EUR" @selected(($settings['currency'] ?? '') === 'EUR')>EUR - Euro</option>
                            <option value="GBP" @selected(($settings['currency'] ?? '') === 'GBP')>GBP - British Pound</option>
                            <option value="INR" @selected(($settings['currency'] ?? '') === 'INR')>INR - Indian Rupee</option>
                            <option value="CAD" @selected(($settings['currency'] ?? '') === 'CAD')>CAD - Canadian Dollar</option>
                            <option value="AUD" @selected(($settings['currency'] ?? '') === 'AUD')>AUD - Australian Dollar</option>
                            <option value="JPY" @selected(($settings['currency'] ?? '') === 'JPY')>JPY - Japanese Yen</option>
                            <option value="SGD" @selected(($settings['currency'] ?? '') === 'SGD')>SGD - Singapore Dollar</option>
                            <option value="AED" @selected(($settings['currency'] ?? '') === 'AED')>AED - UAE Dirham</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label form-label-required">Currency Symbol</label>
                        <input type="text" name="currency_symbol" id="currency-symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" required maxlength="5" class="form-input" placeholder="e.g. $, €, £, ₹, ¥">
                        <p class="text-xs text-neutral-600 mt-1">Auto-filled when you change currency above. You can also type a custom symbol.</p>
                    </div>
                    <div>
                        <label class="form-label form-label-required">Currency Position</label>
                        <select name="currency_position" required class="form-select">
                            <option value="before" @selected(($settings['currency_position'] ?? 'before') === 'before')>Before amount ($99.99)</option>
                            <option value="after" @selected(($settings['currency_position'] ?? '') === 'after')>After amount (99.99$)</option>
                        </select>
                    </div>

                    @php
                        $currCode  = $settings['currency'] ?? 'GBP';
                        $rateKey   = 'exchange_rate_' . strtolower($currCode);
                        $savedRate = $settings[$rateKey] ?? \App\Models\Setting::get($rateKey, '');
                    @endphp
                    <div id="exchange-rate-row" style="{{ $currCode === 'GBP' ? 'display:none' : '' }}">
                        <label class="form-label">Exchange Rate
                            <span class="text-neutral-400 font-normal text-xs">(1 <span id="rate-code">{{ $currCode }}</span> = how many GBP)</span>
                        </label>
                        <div class="flex gap-2 items-center">
                            <input type="number" name="exchange_rate_gbp" id="exchange-rate-input"
                                   value="{{ old('exchange_rate_gbp', $savedRate) }}"
                                   step="0.0001" min="0" class="form-input flex-1" placeholder="Auto-fetched on save if empty">
                            <button type="button" id="fetch-rate-btn" onclick="fetchLiveRate()"
                                    class="btn btn-secondary text-xs whitespace-nowrap" style="height:38px">
                                ↻ Live Rate
                            </button>
                        </div>
                        <p class="text-xs text-neutral-400 mt-1">
                            Leave blank to auto-fetch live rate on save. Prices in GBP ÷ this rate = display price.
                            @if($savedRate)
                                <span class="text-green-600 font-medium">Current rate: {{ number_format((float)$savedRate, 4) }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Currency Converter --}}
        <div class="card mt-6" id="currency-converter-card" x-data="currencyConverter()" style="{{ $currCode === 'GBP' ? 'display:none' : '' }}">
            <div class="px-5 py-3.5 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-neutral-900">Live Currency Converter</h2>
                <span class="text-xs text-neutral-400">Uses the exchange rate above</span>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex items-center gap-3">
                    <input type="number" x-model="amount" @input="convert()" step="0.01" min="0"
                           class="form-input flex-1" placeholder="Enter amount">
                    <select x-model="fromCurrency" @change="convert()" class="form-select" style="width:200px">
                        <option value="GBP">British Pound (£)</option>
                        <option value="USD">US Dollar ($)</option>
                        <option value="EUR">Euro (€)</option>
                        <option value="AED">UAE Dirham (د.إ)</option>
                        <option value="SGD">Singapore Dollar (S$)</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input type="text" x-bind:value="result" readonly
                           class="form-input flex-1 font-semibold" style="background:#f9f9f9" placeholder="Converted amount">
                    <select x-model="toCurrency" @change="convert()" class="form-select" style="width:200px">
                        <option value="USD">US Dollar ($)</option>
                        <option value="EUR">Euro (€)</option>
                        <option value="GBP">British Pound (£)</option>
                        <option value="AED">UAE Dirham (د.إ)</option>
                        <option value="SGD">Singapore Dollar (S$)</option>
                    </select>
                </div>
                <p x-show="rateText" x-text="rateText" class="text-xs text-neutral-500"></p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

    <script>
    // Show/hide exchange rate row + converter; auto-fetch live rate on currency change
    (function(){
        const symbols   = { USD:'$', EUR:'€', GBP:'£', INR:'₹', CAD:'CA$', AUD:'A$', JPY:'¥', SGD:'S$', AED:'د.إ' };
        const sel       = document.getElementById('currency-select');
        const symInp    = document.getElementById('currency-symbol');
        const rateRow   = document.getElementById('exchange-rate-row');
        const rateCode  = document.getElementById('rate-code');
        const convCard  = document.getElementById('currency-converter-card');

        if (!sel) return;
        sel.addEventListener('change', function() {
            const code = this.value;
            if (symbols[code]) symInp.value = symbols[code];
            const notBase = code !== 'GBP';
            rateRow.style.display  = notBase ? '' : 'none';
            convCard.style.display = notBase ? '' : 'none';
            if (rateCode) rateCode.textContent = code;
            if (notBase) fetchLiveRate();
        });
    })();

    async function fetchLiveRate() {
        const code = document.getElementById('currency-select')?.value;
        if (!code || code === 'GBP') return;
        const btn = document.getElementById('fetch-rate-btn');
        const inp = document.getElementById('exchange-rate-input');
        if (btn) btn.textContent = '…';
        try {
            const res  = await fetch('https://open.er-api.com/v6/latest/GBP');
            const data = await res.json();
            if (data.rates && data.rates[code]) {
                const rate = (1 / data.rates[code]).toFixed(4);
                inp.value  = rate;
                if (btn) btn.textContent = '✓ ' + rate;
                setTimeout(() => { if(btn) btn.textContent = '↻ Live Rate'; }, 2000);
            }
        } catch(e) {
            if (btn) btn.textContent = '↻ Live Rate';
        }
    }

    function currencyConverter() {
        return {
            amount:       '',
            fromCurrency: 'GBP',
            toCurrency:   'USD',
            result:       '',
            rateText:     '',
            getRate() {
                const rateInput = parseFloat(document.getElementById('exchange-rate-input')?.value || 0);
                return rateInput > 0 ? rateInput : null;
            },
            convert() {
                const amt  = parseFloat(this.amount);
                const rate = this.getRate();
                if (!amt || !rate) { this.result = ''; this.rateText = ''; return; }

                let converted;
                if (this.fromCurrency === 'GBP' && this.toCurrency !== 'GBP') {
                    converted = amt / rate;
                    this.rateText = `1 ${this.toCurrency} = ${rate.toFixed(4)} GBP`;
                } else if (this.fromCurrency !== 'GBP' && this.toCurrency === 'GBP') {
                    converted = amt * rate;
                    this.rateText = `1 ${this.fromCurrency} = ${rate.toFixed(4)} GBP`;
                } else {
                    converted = amt;
                    this.rateText = '';
                }
                this.result = converted.toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        };
    }
    </script>
</x-layouts.admin>
