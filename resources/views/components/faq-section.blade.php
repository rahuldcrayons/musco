@props(['faqs' => null])

@php
    $cs = currency_symbol();
    $dispatchTime = \App\Models\Setting::get('dispatch_time', '24 hours');
    $deliveryDays = \App\Models\Setting::get('standard_delivery_days', '3-7');
    $returnDays = \App\Models\Setting::get('return_policy_days', 7);

    $defaultFaqs = [
        ['q' => 'How long does delivery take?', 'a' => "We dispatch all orders within {$dispatchTime}. Standard delivery takes {$deliveryDays} business days across the UK. Most orders to England and Wales arrive within 3-4 days."],
        ['q' => 'What is your return policy?', 'a' => "We offer a {$returnDays}-day hassle-free return policy. If you receive a damaged or wrong product, contact us within {$returnDays} days and we will arrange a return and full refund."],
        ['q' => 'Do you offer gift wrapping?', 'a' => 'Yes! We offer premium gift packaging on all orders. Your jewellery arrives beautifully presented, ready to give as a gift.'],
        ['q' => 'Are all products genuine?', 'a' => 'Absolutely. We source directly from brands and authorized distributors. Every product comes with a quality guarantee and manufacturer warranty where applicable.'],
        ['q' => 'How can I track my order?', 'a' => 'Once shipped, you will receive a tracking link via SMS and email. You can also track anytime on our Track Order page using your order number.'],
        ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit and debit cards (Visa, Mastercard, American Express) via Stripe, as well as PayPal. All transactions are fully secured.'],
    ];
    $items = $faqs ?? $defaultFaqs;
@endphp

<section {{ $attributes->merge(['class' => 'py-10 bg-[#f7f7f7]']) }}>
    <div class="container mx-auto px-4">
        <h2 class="text-lg font-bold text-[#222222] mb-5 text-center">Frequently Asked Questions</h2>
        <div class="max-w-3xl mx-auto space-y-2" x-data="{ open: null }">
            @foreach($items as $i => $faq)
                <div class="bg-white border border-[#efefef] rounded overflow-hidden">
                    <button type="button"
                            @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                            class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-[#f7f7f7] transition-colors">
                        <span class="text-sm font-medium text-[#222222] pr-4">{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 text-[#555555] shrink-0 transition-transform duration-200"
                             :class="open === {{ $i }} && 'rotate-180'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse x-cloak>
                        <div class="px-4 pb-3 text-sm text-[#555555] leading-relaxed border-t border-[#efefef]">
                            <p class="pt-3">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQPage Schema (JSON-LD) --}}
<?php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($items)->map(fn($faq) => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ])->toArray(),
];
?>
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
