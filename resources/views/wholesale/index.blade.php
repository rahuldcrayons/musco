<x-layouts.app>
    <x-slot name="title">Wholesale - {{ config('app.name') }}</x-slot>

    <div class="bg-[#f7f7f7] min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto text-center">
                <h1 class="text-2xl font-bold text-[#222222] mb-4">Wholesale Enquiry</h1>
                <p class="text-sm text-[#555555] mb-6">Interested in buying in bulk? We offer competitive wholesale pricing for businesses and resellers.</p>

                <div class="bg-white rounded-lg border border-[#efefef] p-6 text-left">
                    <h2 class="text-base font-semibold text-[#222222] mb-3">Get in touch</h2>
                    <p class="text-sm text-[#555555] mb-4">Contact us for wholesale pricing, minimum order quantities, and custom packaging options.</p>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-[#B76E79]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:wholesale@musco.com" class="text-[#B76E79] hover:text-[#c29958]">wholesale@musco.com</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-[#B76E79]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="https://wa.me/919354567705" class="text-[#B76E79] hover:text-[#c29958]">WhatsApp: +91 93545 67705</a>
                        </div>
                    </div>

                    <a href="{{ route('contact') }}" class="mt-5 inline-block px-5 py-2.5 bg-[#B76E79] hover:bg-[#c29958]/90 text-white text-sm font-semibold rounded transition-colors">
                        Send Enquiry
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
