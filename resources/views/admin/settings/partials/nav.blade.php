@php
    $tabs = [
        'general' => [
            'label' => 'General',
            'route' => 'admin.settings.general',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ],
        'payment' => [
            'label' => 'Payment',
            'route' => 'admin.settings.payment',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        ],
        'shipping' => [
            'label' => 'Shipping',
            'route' => 'admin.settings.shipping',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
        ],
        'tax' => [
            'label' => 'Tax',
            'route' => 'admin.settings.tax',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>',
        ],
        'email' => [
            'label' => 'Email',
            'route' => 'admin.settings.email',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        ],
        'seo' => [
            'label' => 'SEO',
            'route' => 'admin.settings.seo',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
        ],
        'product-card' => [
            'label' => 'Features',
            'route' => 'admin.settings.product-card',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>',
        ],
    ];
@endphp

<div x-data="settingsImport()" class="card mb-6">
    <div class="flex overflow-x-auto scrollbar-thin">
        @foreach($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
               class="flex items-center gap-1.5 px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ ($active ?? '') === $key ? 'text-neutral-900 border-neutral-900' : 'text-neutral-800 border-transparent hover:text-neutral-900 hover:border-neutral-400' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                {{ $tab['label'] }}
            </a>
        @endforeach

        <!-- Import Button -->
        <button @click="open = true"
                type="button"
                class="flex items-center gap-1.5 px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-neutral-800 hover:text-neutral-900 hover:border-neutral-400 transition-colors ml-auto">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import
        </button>
    </div>

    <!-- Import Modal -->
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="display:none;">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50" @click="close()"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 z-10" @click.stop>

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900">Import Settings</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Upload an Excel file to bulk-update settings</p>
                </div>
                <button @click="close()" type="button" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-5">

                <!-- File Upload Zone -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-neutral-700">Excel File (.xlsx, .xls)</label>
                        <a href="{{ route('admin.settings.import.template') }}"
                           class="text-xs text-blue-600 hover:text-blue-800 underline">
                            Download template
                        </a>
                    </div>
                    <div class="border-2 border-dashed border-neutral-300 rounded-lg p-8 text-center cursor-pointer hover:border-neutral-400 transition-colors"
                         @dragover.prevent="dragover = true"
                         @dragleave.prevent="dragover = false"
                         @drop.prevent="handleDrop($event)"
                         :class="dragover ? 'border-neutral-500 bg-neutral-50' : ''"
                         @click="$refs.fileInput.click()">
                        <input type="file" x-ref="fileInput" class="hidden"
                               accept=".xlsx,.xls"
                               @change="handleFile($event)">
                        <template x-if="!file">
                            <div>
                                <svg class="w-10 h-10 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm font-medium text-neutral-600">Click to browse or drag & drop</p>
                                <p class="text-xs text-neutral-400 mt-1">.xlsx or .xls — columns: group, key, value</p>
                            </div>
                        </template>
                        <template x-if="file">
                            <div class="flex items-center justify-center gap-3">
                                <svg class="w-8 h-8 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-neutral-800" x-text="file.name"></p>
                                    <p class="text-xs text-neutral-500" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                                </div>
                                <button type="button" @click.stop="file = null; $refs.fileInput.value = ''"
                                        class="ml-2 text-neutral-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Imported Data Preview -->
                <template x-if="importedRows.length > 0">
                    <div>
                        <p class="text-xs font-semibold text-neutral-600 mb-2 uppercase tracking-wide">Imported Settings</p>
                        <div class="border border-neutral-200 rounded-lg overflow-hidden max-h-52 overflow-y-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-neutral-50 sticky top-0">
                                    <tr>
                                        <th class="text-left px-3 py-2 text-neutral-600 font-medium">Group</th>
                                        <th class="text-left px-3 py-2 text-neutral-600 font-medium">Key</th>
                                        <th class="text-left px-3 py-2 text-neutral-600 font-medium">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    <template x-for="(row, i) in importedRows" :key="i">
                                        <tr class="hover:bg-neutral-50">
                                            <td class="px-3 py-1.5 text-neutral-500" x-text="row.group"></td>
                                            <td class="px-3 py-1.5 font-mono text-neutral-800" x-text="row.key"></td>
                                            <td class="px-3 py-1.5 text-neutral-600 max-w-[150px] truncate" x-text="row.value"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-neutral-400 mt-1" x-text="importedRows.length + ' setting(s) updated'"></p>
                    </div>
                </template>

                <!-- Result Message -->
                <template x-if="result">
                    <div :class="result.success ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                         class="border rounded-lg p-3 text-sm">
                        <p class="font-medium" x-text="result.message"></p>
                        <p class="text-xs mt-1 opacity-75" x-show="result.detail" x-text="result.detail"></p>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-neutral-200 bg-neutral-50 rounded-b-xl">
                <button type="button" @click="close()" class="btn btn-secondary" :disabled="loading">Cancel</button>
                <button type="button" @click="upload()"
                        class="btn btn-primary"
                        :disabled="!file || loading">
                    <template x-if="loading">
                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Importing…' : 'Import Settings'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function settingsImport() {
    return {
        open: false,
        file: null,
        loading: false,
        dragover: false,
        result: null,
        importedRows: [],

        close() {
            if (this.result && this.result.success) {
                window.location.reload();
                return;
            }
            this.open = false;
            this.file = null;
            this.result = null;
            this.loading = false;
            this.importedRows = [];
        },

        handleFile(e) {
            const f = e.target.files[0];
            if (f) { this.file = f; this.result = null; this.importedRows = []; }
        },

        handleDrop(e) {
            this.dragover = false;
            const f = e.dataTransfer.files[0];
            if (f && (f.name.endsWith('.xlsx') || f.name.endsWith('.xls'))) {
                this.file = f;
                this.result = null;
                this.importedRows = [];
            }
        },

        async upload() {
            if (!this.file) return;
            this.loading = true;
            this.result = null;
            this.importedRows = [];

            const form = new FormData();
            form.append('file', this.file);

            try {
                const res = await fetch('{{ route("admin.settings.import") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: form,
                });

                let data;
                try {
                    data = await res.json();
                } catch {
                    throw new Error('Server returned an unexpected response (HTTP ' + res.status + ')');
                }

                if (!res.ok) {
                    // Laravel validation errors (422)
                    const firstError = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : (data.message || 'Validation failed');
                    this.result = { success: false, message: firstError };
                    return;
                }

                this.result = data;
                if (data.success && data.rows) {
                    this.importedRows = data.rows;
                }
            } catch (e) {
                this.result = { success: false, message: e.message || 'Upload failed. Please try again.' };
            } finally {
                this.loading = false;
            }
        },
    };
}

/* ─── Currency Formatter ───────────────────────────────────────────── */
window.formatGBP = function(amount, showSymbol = true) {
    const num = parseFloat(amount);
    if (isNaN(num)) return showSymbol ? '£0.00' : '0.00';
    return new Intl.NumberFormat('en-GB', {
        style: showSymbol ? 'currency' : 'decimal',
        currency: 'GBP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num);
};

/* ─── USD → GBP Converter ─────────────────────────────────────────────────── */
window._usdGbpRate = null; // cached in-page

/**
 * Fetch live USD→GBP rate (cached per page-load, falls back to stored rate).
 * Returns a Promise<number>.
 */
window.getUsdGbpRate = async function() {
    if (window._usdGbpRate) return window._usdGbpRate;
    try {
        const res  = await fetch('https://open.er-api.com/v6/latest/USD', { cache: 'force-cache' });
        const data = await res.json();
        if (data.rates && data.rates.GBP) {
            window._usdGbpRate = data.rates.GBP;
            return window._usdGbpRate;
        }
    } catch (_) {}
    // Fallback: rate stored in PHP setting (printed at page load)
    window._usdGbpRate = {{ (float) \App\Models\Setting::get('usd_gbp_rate', '0.79') }};
    return window._usdGbpRate;
};

/**
 * Convert USD to GBP.
 * @param {number} usd - Amount in USD
 * @param {boolean} formatted - If true returns "£12.50" string
 * @returns {Promise<number|string>}
 *
 * Usage:
 *   const inr = await convertUSDtoGBP(10);         // 840.5  (number)
 *   const str = await convertUSDtoGBP(10, true);   // "£8.40"
 */
window.convertUSDtoGBP = async function(usd, formatted = false) {
    const rate = await window.getUsdGbpRate();
    const gbp  = Math.round(parseFloat(usd) * rate * 100) / 100;
    return formatted ? window.formatGBP(gbp) : gbp;
};

/* Quick sync version (uses cached rate, falls back to 0.79) */
window.convertUSDtoGBPSync = function(usd, formatted = false) {
    const rate = window._usdGbpRate || {{ (float) \App\Models\Setting::get('usd_gbp_rate', '0.79') }};
    const gbp  = Math.round(parseFloat(usd) * rate * 100) / 100;
    return formatted ? window.formatGBP(gbp) : gbp;
};
</script>
