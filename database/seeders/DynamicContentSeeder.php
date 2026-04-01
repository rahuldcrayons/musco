<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DynamicContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFaqs();
        $this->seedContentPages();
        $this->seedTestimonials();

        $this->command->info('Dynamic content seeded: FAQs, content pages, testimonials');
    }

    private function seedFaqs(): void
    {
        $faqs = [
            // Ordering
            ['category' => 'Ordering', 'question' => 'How do I place an order?', 'answer' => "Simply browse our products, add items to your cart, and proceed to checkout. You'll need to create an account or sign in, enter your shipping details, and complete the payment. You'll receive an order confirmation email once your order is placed.", 'position' => 1],
            ['category' => 'Ordering', 'question' => 'What payment methods do you accept?', 'answer' => 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. All payments are processed securely through our payment partners.', 'position' => 2],
            ['category' => 'Ordering', 'question' => 'How can I track my order?', 'answer' => "Once your order ships, you'll receive an email with tracking information. You can also track your order by logging into your account and visiting the Orders section, or by using our order tracking page.", 'position' => 3],

            // Shipping
            ['category' => 'Shipping', 'question' => 'How long does shipping take?', 'answer' => 'Shipping times vary depending on your location and chosen shipping method. Standard shipping typically takes 5-7 business days, while express shipping takes 2-3 business days. International shipping may take 7-14 business days.', 'position' => 4],
            ['category' => 'Shipping', 'question' => 'Do you ship internationally?', 'answer' => "Yes, we ship to over 100 countries worldwide. Shipping costs and delivery times vary by destination. You can see the exact shipping costs at checkout.", 'position' => 5],

            // Returns & Refunds
            ['category' => 'Returns & Refunds', 'question' => 'What is your return policy?', 'answer' => 'We offer a 7-day return policy for most items. Products must be unused and in their original packaging. Please visit our Returns Policy page for full details.', 'position' => 6],
            ['category' => 'Returns & Refunds', 'question' => 'How do I request a refund?', 'answer' => "To request a refund, log into your account, go to your Orders, and select the order you wish to return. Click \"Request Return\" and follow the instructions. Once we receive and inspect the returned item, your refund will be processed within 5-7 business days.", 'position' => 7],

            // Account
            ['category' => 'Account', 'question' => 'How do I create an account?', 'answer' => "Click the \"Sign Up\" button at the top of the page and fill in your details. You can also create an account during checkout. Having an account allows you to track orders, save addresses, and earn rewards.", 'position' => 8],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }

    private function seedContentPages(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'is_published' => true,
                'published_at' => now(),
                'content' => '<p>Founded with a passion for fine craftsmanship and timeless design, MusCo started with a simple mission — to bring exquisite, certified jewellery to every doorstep across India.</p><p>Today, we\'ve grown into a trusted destination for thousands of customers. We partner directly with master artisans and certified manufacturers to ensure every piece meets the highest standards of purity and craftsmanship.</p><p>From everyday gold essentials to stunning diamond collections, our curated range spans rings, necklaces, earrings, bangles and more — all BIS hallmarked, competitively priced, and delivered with an authenticity guarantee.</p>',
            ],
            [
                'title' => 'Returns Policy',
                'slug' => 'returns-policy',
                'is_published' => true,
                'published_at' => now(),
                'content' => <<<'HTML'
<h2>7-Day Return Policy</h2>
<p>We offer a 7-day return policy on most items. You can return products within 7 days of delivery for a full refund or exchange.</p>

<h2>Return Eligibility</h2>
<p>To be eligible for a return, your item must be:</p>
<ul>
    <li>In the same condition as received — unused and in working order</li>
    <li>In its original packaging with all accessories and manuals included</li>
    <li>Free from physical damage, scratches, or signs of use</li>
    <li>Accompanied by the invoice or proof of purchase</li>
</ul>

<h2>Non-Returnable Items</h2>
<p>The following items cannot be returned:</p>
<ul>
    <li>Products with broken or tampered warranty seals</li>
    <li>Items damaged due to misuse, power surges, or improper handling</li>
    <li>Installed or activated software and digital products</li>
    <li>Products with missing accessories, cables, or original packaging</li>
    <li>Items marked as final sale or clearance</li>
    <li>Gift cards and prepaid vouchers</li>
</ul>

<h2>How to Return an Item</h2>
<p><strong>Step 1: Request Return</strong> — Log into your account and go to your order history to request a return.</p>
<p><strong>Step 2: Ship Item</strong> — Pack the item securely and ship it using the provided return label.</p>
<p><strong>Step 3: Get Refund</strong> — Once received and inspected, your refund will be processed within 5-7 days.</p>

<h2>Refunds</h2>
<p>Once your return is received and inspected, we will send you an email to notify you of the approval or rejection of your refund.</p>
<p>If approved, your refund will be processed, and a credit will automatically be applied to your original payment method within 5-7 business days.</p>

<h2>Return Shipping</h2>
<p>For defective or incorrect items, we will provide a prepaid return shipping label. For other returns, the customer is responsible for return shipping costs.</p>

<h2>Exchanges</h2>
<p>If you need a different variant or model, we recommend returning the item for a refund and placing a new order for the fastest delivery.</p>

<h2>Damaged or Defective Items</h2>
<p>If you receive a damaged or defective item, please contact us within 48 hours of delivery with photos of the damage. We will arrange for a replacement or refund.</p>

<h2>Questions?</h2>
<p>Our support team is here to help with your returns. <a href="/contact">Contact us</a> for assistance.</p>
HTML,
            ],
            [
                'title' => 'Shipping Information',
                'slug' => 'shipping-policy',
                'is_published' => true,
                'published_at' => now(),
                'content' => <<<'HTML'
<h2>Delivery Options</h2>
<p>We offer multiple delivery options to suit your needs:</p>
<ul>
    <li><strong>Standard Delivery</strong> — 5-7 business days. FREE on orders above ₹499, otherwise ₹49.</li>
    <li><strong>Express Delivery</strong> — 2-3 business days for ₹99. Available in select cities.</li>
    <li><strong>Same Day Delivery</strong> — Order before 12 PM for ₹149. Mumbai, Delhi, Bangalore only.</li>
    <li><strong>Cash on Delivery</strong> — Pay when you receive. Available on orders up to ₹5,000.</li>
</ul>

<h2>Order Processing</h2>
<p>Orders placed before 2:00 PM IST on business days are typically processed and shipped the same day. Orders placed after 2:00 PM IST or on weekends/holidays will be processed the next business day.</p>

<h2>Tracking Your Order</h2>
<p>Once your order ships, you'll receive an SMS and email with tracking details. You can also log into your account and check your order history, or use our order tracking page.</p>

<h2>Delivery Coverage</h2>
<p>We deliver across all major cities and towns in India. You can check delivery availability for your pincode during checkout. Remote areas may take 1-2 additional business days.</p>

<h2>Missed Delivery</h2>
<p>Our delivery partner will attempt delivery up to 3 times. If you're unavailable, they'll contact you on your registered phone number. You can also request a reschedule by contacting our support team.</p>

<h2>Address Changes</h2>
<p>You can change the delivery address before the order is shipped. Once shipped, address changes are not possible. To change the address, please contact our customer support immediately with your order number.</p>

<h2>Damaged or Lost Packages</h2>
<p>If your package arrives damaged or is lost in transit, please contact us within 7 days of the expected delivery date. We will work with the delivery partner to resolve the issue and arrange a replacement or refund as quickly as possible.</p>

<h2>Shipping Restrictions</h2>
<p>Some oversized items or bundled sets may have shipping restrictions to certain pincodes. You'll be notified at checkout if any items in your cart cannot be shipped to your location.</p>
HTML,
            ],
            [
                'title' => 'Careers',
                'slug' => 'careers',
                'is_published' => true,
                'published_at' => now(),
                'content' => 'Join our team and help us bring the best products to customers across India.',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'name' => 'Priya S.',
                'title' => 'Verified Buyer',
                'content' => 'Absolutely love shopping here! The clothes are always top quality, packaging is excellent, and delivery is super fast. My go-to store for all my shopping needs.',
                'rating' => 5,
                'position' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Aisha M.',
                'title' => 'Verified Buyer',
                'content' => "Best prices I've found for quality products. I've been ordering for 6 months now and never been disappointed. The quality is great, descriptions are accurate, and customer service is outstanding!",
                'rating' => 5,
                'position' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Rahul K.',
                'title' => 'Verified Buyer',
                'content' => 'I was skeptical at first, but MusCo exceeded my expectations. The packaging was secure, quality was amazing, and arrived within 3 days. Highly recommend!',
                'rating' => 5,
                'position' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
