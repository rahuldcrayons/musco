@component('mail::message')
# Welcome to Jikra!

Hi {{ $user->first_name }},

We are thrilled to have you join the Jikra family! Thank you for creating your account with us.

At Jikra, we are passionate about providing quality mobile accessories and gadgets. From everyday essentials to the latest tech accessories, we have everything to keep you connected and powered up.

---

## Here Is What You Can Look Forward To

- **Curated Collections** -- Handpicked accessories for every device and need
- **Quality You Can Trust** -- Durable, reliable products you can count on
- **Exclusive Deals** -- Member-only discounts and early access to sales
- **Easy Returns** -- Hassle-free returns within 7 days of delivery

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

We cannot wait to help you find the perfect accessories for your devices!

Warm regards,
**Jikra**
@endcomponent
