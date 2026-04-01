@component('mail::message')
# Welcome to {{ \App\Models\Setting::get('site_name', 'MusCo') }}!

Hi {{ $user->first_name }},

We are thrilled to have you join the {{ \App\Models\Setting::get('site_name', 'MusCo') }} family! Thank you for creating your account with us.

{{ \App\Models\Setting::get('welcome_email_intro', 'At ' . \App\Models\Setting::get('site_name', 'MusCo') . ', we are passionate about crafting exquisite jewellery for every occasion. From everyday gold essentials to stunning diamond collections, we have something special for every moment.') }}

---

## Here Is What You Can Look Forward To

- **Curated Collections** -- Handpicked jewellery for every occasion and style
- **Certified Quality** -- BIS hallmarked gold and certified diamonds you can trust
- **Exclusive Deals** -- Member-only discounts and early access to new collections
- **Easy Returns** -- Hassle-free returns within 15 days of delivery

@component('mail::button', ['url' => url('/shop')])
Start Shopping
@endcomponent

---

## Your Account Details

**Name:** {{ $user->full_name }}
**Email:** {{ $user->email }}

You can manage your profile, track orders, and save your favorite items all from your account dashboard.

@component('mail::button', ['url' => url('/account'), 'color' => 'success'])
Visit Your Account
@endcomponent

If you have any questions or need assistance, our friendly support team is always ready to help. Just reply to this email or visit our help center.

We cannot wait to help you find the perfect jewellery for your special moments!

Warm regards,
**{{ \App\Models\Setting::get('site_name', 'MusCo') }}**
@endcomponent
