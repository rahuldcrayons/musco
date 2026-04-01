<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:16px 0;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:8px;overflow:hidden;">

<tr><td style="background:#fff;padding:16px 20px;text-align:center;border-bottom:3px solid #222222;">
<span style="font-family:Outfit,sans-serif; color:#B76E79; font-size:24px; font-weight:700; letter-spacing:-0.02em;">Mus<span style="color:#2b2b2b;">Co</span></span>
</td></tr>

<tr><td style="background:#B76E79;padding:20px;text-align:center;">
<div style="width:40px;height:40px;background:#fff;border-radius:50%;display:inline-block;line-height:40px;font-size:22px;color:#B76E79;font-weight:bold;">&#10003;</div>
<h1 style="color:#fff;font-size:20px;margin:10px 0 2px;">Order Confirmed!</h1>
<p style="color:rgba(255,255,255,0.7);font-size:13px;margin:0;">Your order has been received</p>
</td></tr>

<tr><td style="padding:20px;">

<p style="font-size:14px;color:#333;margin:0 0 16px;">Hi <strong>{{ $order->user->first_name ?? $order->guest_name ?? 'there' }}</strong>, thanks for shopping with {{ \App\Models\Setting::get('site_name', 'MusCo') }}!</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:6px;margin-bottom:16px;">
<tr>
<td style="padding:12px 14px;border-bottom:1px solid #eee;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Order</span><br>
<strong style="font-size:14px;color:#111;">#{{ $order->order_number }}</strong>
</td>
<td style="padding:12px 14px;border-bottom:1px solid #eee;text-align:right;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Date</span><br>
<strong style="font-size:14px;color:#111;">{{ $order->created_at->format('d M Y') }}</strong>
</td>
</tr>
<tr>
<td style="padding:12px 14px;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Payment</span><br>
<strong style="font-size:14px;color:#B76E79;">{{ $order->payment_status === 'paid' ? 'Paid' : 'Cash on Delivery' }}</strong>
</td>
<td style="padding:12px 14px;text-align:right;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Total</span><br>
<strong style="font-size:18px;color:#B76E79;">{{ format_price($order->total) }}</strong>
</td>
</tr>
</table>

<p style="font-size:13px;font-weight:bold;color:#111;margin:0 0 8px;padding-bottom:6px;border-bottom:2px solid #B76E79;">ITEMS</p>

@foreach($order->items as $item)
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #f0f0f0;">
<tr>
<td style="vertical-align:top;padding:4px 0;">
<p style="margin:0;font-size:13px;font-weight:600;color:#111;">{{ $item->product_name }}</p>
@if($item->variant_name)<p style="margin:2px 0 0;font-size:11px;color:#888;">{{ $item->variant_name }}</p>@endif
<p style="margin:2px 0 0;font-size:11px;color:#888;">Qty: {{ $item->quantity }}</p>
</td>
<td style="vertical-align:top;text-align:right;padding:4px 0;white-space:nowrap;">
<strong style="font-size:14px;color:#111;">{{ format_price($item->total) }}</strong>
</td>
</tr>
</table>
@endforeach

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:6px;margin-top:12px;">
<tr><td style="padding:6px 14px;font-size:13px;color:#555;">Subtotal</td><td style="padding:6px 14px;text-align:right;font-size:13px;color:#333;">{{ format_price($order->subtotal) }}</td></tr>
@if($order->discount > 0)
<tr><td style="padding:4px 14px;font-size:13px;color:#B76E79;">Discount</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:#B76E79;">-{{ format_price($order->discount) }}</td></tr>
@endif
@if($order->tax > 0)
<tr><td style="padding:4px 14px;font-size:13px;color:#555;">Tax</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:#333;">{{ format_price($order->tax) }}</td></tr>
@endif
<tr><td style="padding:4px 14px;font-size:13px;color:#555;">Shipping</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:{{ $order->shipping_cost > 0 ? '#333' : '#B76E79' }};">{{ $order->shipping_cost > 0 ? format_price($order->shipping_cost) : 'FREE' }}</td></tr>
<tr><td colspan="2" style="padding:0 14px;"><div style="border-top:1px solid #ddd;"></div></td></tr>
<tr><td style="padding:8px 14px;font-size:15px;font-weight:700;color:#111;">Total</td><td style="padding:8px 14px;text-align:right;font-size:17px;font-weight:700;color:#B76E79;">{{ format_price($order->total) }}</td></tr>
</table>

@if($order->shipping_address_snapshot)
<p style="font-size:13px;font-weight:bold;color:#111;margin:20px 0 8px;padding-bottom:6px;border-bottom:2px solid #B76E79;">DELIVERY ADDRESS</p>
<p style="font-size:13px;color:#333;line-height:1.5;margin:0;background:#f8f9fa;border-radius:6px;padding:10px 14px;">
<strong>{{ $order->shipping_address_snapshot['name'] ?? '' }}</strong><br>
{{ $order->shipping_address_snapshot['address_line_1'] ?? '' }}@if(!empty($order->shipping_address_snapshot['address_line_2'])), {{ $order->shipping_address_snapshot['address_line_2'] }}@endif<br>
{{ $order->shipping_address_snapshot['city'] ?? '' }}, {{ $order->shipping_address_snapshot['state'] ?? '' }} {{ $order->shipping_address_snapshot['postal_code'] ?? '' }}
</p>
@endif

<div style="text-align:center;margin:24px 0 8px;">
<a href="{{ url('/account/orders') }}" style="background:#B76E79;color:#fff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:14px;font-weight:600;display:inline-block;">Track Your Order</a>
</div>

<p style="font-size:11px;color:#999;text-align:center;margin:16px 0 0;">Need help? Reply to this email or WhatsApp us at {{ \App\Models\Setting::get('site_phone', '+91 93545 67705') }}</p>

</td></tr>

<tr><td style="background:#222222;padding:14px 20px;text-align:center;">
<a href="{{ url('/') }}" style="color:#B76E79;font-size:12px;text-decoration:none;margin:0 8px;">Shop</a>
<a href="{{ url('/account/orders') }}" style="color:#B76E79;font-size:12px;text-decoration:none;margin:0 8px;">Orders</a>
<a href="{{ url('/contact') }}" style="color:#B76E79;font-size:12px;text-decoration:none;margin:0 8px;">Contact</a>
<p style="color:#7a9a9e;font-size:10px;margin:8px 0 0;">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'MusCo') }}. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
