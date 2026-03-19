{{-- Exit-Intent Popup — First-time visitor 10% discount --}}
@guest
<div x-data="exitIntent()" x-show="show" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
     @keydown.escape.window="dismiss()">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="dismiss()"></div>

    {{-- Modal --}}
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0">

        {{-- Close button --}}
        <button @click="dismiss()" class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Content --}}
        <div class="p-6 sm:p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-[#FFF3E0] rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-[#F8931D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
            </div>

            <h2 class="text-xl sm:text-2xl font-bold text-[#0F1111] mb-2">Wait! Don't leave yet</h2>
            <p class="text-sm text-[#565959] mb-5">Get <span class="font-bold text-[#CC0C39]">10% OFF</span> your first order. Enter your email and we'll send you the discount code instantly.</p>

            {{-- Email form --}}
            <form @submit.prevent="submitEmail()" class="space-y-3">
                <input type="email" x-model="email" required
                       placeholder="Enter your email address"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#F8931D] focus:border-[#F8931D] outline-none">

                <button type="submit" :disabled="submitting"
                        class="w-full py-3 bg-[#F8931D] hover:bg-[#E07E0A] text-white font-bold rounded-lg text-sm transition-colors disabled:opacity-50">
                    <span x-show="!submitting">GET 10% OFF</span>
                    <span x-show="submitting">Sending...</span>
                </button>
            </form>

            <p class="text-[11px] text-[#565959] mt-3">No spam, ever. Unsubscribe anytime.</p>

            {{-- Success message --}}
            <div x-show="success" x-cloak class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700 font-medium">Check your email! Your 10% discount code is on its way.</p>
            </div>

            <button @click="dismiss()" class="mt-4 text-xs text-[#565959] hover:text-[#0F1111] underline">No thanks, I'll pay full price</button>
        </div>
    </div>
</div>

<script>
function exitIntent() {
    return {
        show: false,
        email: '',
        submitting: false,
        success: false,

        init() {
            // Don't show if already dismissed or is returning visitor
            if (localStorage.getItem('exit_popup_dismissed') || localStorage.getItem('exit_popup_subscribed')) return;

            // Desktop: mouse leaves viewport (exit intent)
            document.addEventListener('mouseout', (e) => {
                if (e.clientY <= 0 && !this.show) {
                    this.show = true;
                }
            }, { once: true });

            // Mobile: show after 45 seconds of browsing
            setTimeout(() => {
                if (!this.show && !localStorage.getItem('exit_popup_dismissed')) {
                    this.show = true;
                }
            }, 45000);
        },

        dismiss() {
            this.show = false;
            localStorage.setItem('exit_popup_dismissed', Date.now());
        },

        async submitEmail() {
            if (!this.email || this.submitting) return;
            this.submitting = true;

            try {
                const res = await fetch('{{ route("newsletter.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        email: this.email,
                        source: 'exit_intent_popup',
                        first_buyer_discount: true,
                    }),
                });

                this.success = true;
                localStorage.setItem('exit_popup_subscribed', '1');
                setTimeout(() => this.dismiss(), 4000);
            } catch (e) {
                console.error(e);
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endguest
