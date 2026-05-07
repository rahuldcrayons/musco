<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Order Received</title>
</head>
<body style="margin:0;padding:0;background:#f0ebe6;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0ebe6;padding:16px 0;">
<tr><td align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:8px;overflow:hidden;">

<tr><td style="background:#202a40;padding:16px 20px;text-align:center;border-bottom:3px solid #506282;">
<span style="font-family:'Playfair Display',Georgia,serif; color:#f0ebe6; font-size:24px; font-weight:700; letter-spacing:-0.02em;">Trendymus</span>
</td></tr>

<tr><td style="background:#506282;padding:20px;text-align:center;">
<div style="width:40px;height:40px;background:#f0ebe6;border-radius:50%;display:inline-block;line-height:40px;font-size:22px;color:#202a40;font-weight:bold;">&#128276;</div>
<h1 style="color:#fff;font-size:20px;margin:10px 0 2px;">New Order Received!</h1>
<p style="color:rgba(255,255,255,0.7);font-size:13px;margin:0;">#{{ $order->order_number }} &mdash; {{ $order->created_at->format('d M Y, H:i') }}</p>
</td></tr>

<tr><td style="padding:20px;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f8f6;border-radius:6px;margin-bottom:16px;">
<tr>
<td style="padding:12px 14px;border-bottom:1px solid #eee;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Customer</span><br>
<strong style="font-size:14px;color:#202a40;">{{ $order->user->first_name ?? $order->guest_name ?? 'Guest' }} {{ $order->user->last_name ?? '' }}</strong>
</td>
<td style="padding:12px 14px;border-bottom:1px solid #eee;text-align:right;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Email</span><br>
<strong style="font-size:13px;color:#202a40;">{{ $order->user->email ?? $order->guest_email ?? 'N/A' }}</strong>
</td>
</tr>
<tr>
<td style="padding:12px 14px;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Phone</span><br>
<strong style="font-size:14px;color:#202a40;">{{ $order->user->phone ?? $order->guest_phone ?? 'N/A' }}</strong>
</td>
<td style="padding:12px 14px;text-align:right;">
<span style="font-size:11px;color:#888;text-transform:uppercase;">Payment</span><br>
<strong style="font-size:14px;color:{{ $order->payment_status === 'paid' ? '#28a745' : '#dc3545' }};">{{ $order->payment_status === 'paid' ? 'Paid' : 'Pending' }}</strong>
</td>
</tr>
</table>

<p style="font-size:13px;font-weight:bold;color:#202a40;margin:0 0 8px;padding-bottom:6px;border-bottom:2px solid #202a40;">ORDER ITEMS</p>

@foreach($order->items as $item)
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:6px;padding-bottom:6px;border-bottom:1px solid #f0f0f0;">
<tr>
<td style="vertical-align:top;padding:4px 0;">
<p style="margin:0;font-size:13px;font-weight:600;color:#202a40;">{{ $item->product_name }}</p>
@if($item->variant_name)<p style="margin:2px 0 0;font-size:11px;color:#888;">{{ $item->variant_name }}</p>@endif
<p style="margin:2px 0 0;font-size:11px;color:#888;">Qty: {{ $item->quantity }}</p>
</td>
<td style="vertical-align:top;text-align:right;padding:4px 0;white-space:nowrap;">
<strong style="font-size:14px;color:#202a40;">{{ format_price($item->total) }}</strong>
</td>
</tr>
</table>
@endforeach

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f8f6;border-radius:6px;margin-top:12px;">
<tr><td style="padding:6px 14px;font-size:13px;color:#555;">Subtotal</td><td style="padding:6px 14px;text-align:right;font-size:13px;color:#333;">{{ format_price($order->subtotal) }}</td></tr>
@if($order->discount > 0)
<tr><td style="padding:4px 14px;font-size:13px;color:#506282;">Discount</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:#506282;">-{{ format_price($order->discount) }}</td></tr>
@endif
@if($order->tax > 0)
<tr><td style="padding:4px 14px;font-size:13px;color:#555;">Tax</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:#333;">{{ format_price($order->tax) }}</td></tr>
@endif
<tr><td style="padding:4px 14px;font-size:13px;color:#555;">Shipping</td><td style="padding:4px 14px;text-align:right;font-size:13px;color:{{ $order->shipping_cost > 0 ? '#333' : '#28a745' }};">{{ $order->shipping_cost > 0 ? format_price($order->shipping_cost) : 'FREE' }}</td></tr>
<tr><td colspan="2" style="padding:0 14px;"><div style="border-top:1px solid #ddd;"></div></td></tr>
<tr><td style="padding:8px 14px;font-size:15px;font-weight:700;color:#202a40;">Total</td><td style="padding:8px 14px;text-align:right;font-size:17px;font-weight:700;color:#202a40;">{{ format_price($order->total) }}</td></tr>
</table>

@if($order->shipping_address_snapshot)
<p style="font-size:13px;font-weight:bold;color:#202a40;margin:20px 0 8px;padding-bottom:6px;border-bottom:2px solid #202a40;">DELIVERY ADDRESS</p>
<p style="font-size:13px;color:#333;line-height:1.5;margin:0;background:#f8f8f6;border-radius:6px;padding:10px 14px;">
<strong>{{ $order->shipping_address_snapshot['name'] ?? '' }}</strong><br>
{{ $order->shipping_address_snapshot['address_line_1'] ?? '' }}@if(!empty($order->shipping_address_snapshot['address_line_2'])), {{ $order->shipping_address_snapshot['address_line_2'] }}@endif<br>
{{ $order->shipping_address_snapshot['city'] ?? '' }}, {{ $order->shipping_address_snapshot['state'] ?? '' }} {{ $order->shipping_address_snapshot['postal_code'] ?? '' }}
</p>
@endif

<div style="text-align:center;margin:24px 0 8px;">
<a href="{{ url('/admin/orders/' . $order->id) }}" style="background:#202a40;color:#f0ebe6;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:14px;font-weight:600;display:inline-block;">Manage Order</a>
</div>

</td></tr>

<tr><td style="background:#202a40;padding:14px 20px;text-align:center;">
<a href="{{ url('/') }}" style="color:#f0ebe6;font-size:12px;text-decoration:none;margin:0 8px;">Shop</a>
<a href="{{ url('/admin/orders') }}" style="color:#f0ebe6;font-size:12px;text-decoration:none;margin:0 8px;">Orders</a>
<a href="{{ url('/admin') }}" style="color:#f0ebe6;font-size:12px;text-decoration:none;margin:0 8px;">Dashboard</a>
<p style="color:#506282;font-size:10px;margin:8px 0 0;">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Trendymus') }}. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
