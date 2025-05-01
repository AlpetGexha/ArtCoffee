@component('mail::message')
# You've Received a Coffee Art Shop Gift Card!

<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('images/gift-card.png') }}" alt="Gift Card" style="max-width: 300px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
</div>

## Hello there!

<div style="display: flex; align-items: center; margin-bottom: 15px;">
    <span style="font-size: 24px; margin-right: 10px;">🎁</span>
    <span><strong>{{ $senderName }}</strong> has sent you a gift card worth <strong>{{ $amount }}</strong>!</span>
</div>

@if(!empty($message))
## Personal Message:
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #007bff;">
    <span style="font-size: 20px; margin-right: 10px;">💌</span>
    <em>"{{ $message }}"</em>
</div>
@endif

@component('mail::panel')
## Gift Card Details
<div style="display: flex; align-items: center; margin-bottom: 10px;">
    <span style="font-size: 18px; margin-right: 10px;">💰</span>
    <span><strong>Amount:</strong> {{ $amount }}</span>
</div>
<div style="display: flex; align-items: center; margin-bottom: 10px;">
    <span style="font-size: 18px; margin-right: 10px;">🎉</span>
    <span><strong>Occasion:</strong> {{ $giftCard->occasion ?? 'Gift' }}</span>
</div>
<div style="display: flex; align-items: center;">
    <span style="font-size: 18px; margin-right: 10px;">📅</span>
    <span><strong>Expires On:</strong> {{ $expirationDate }}</span>
</div>
@endcomponent

<div style="display: flex; align-items: center; margin-bottom: 20px;">
    <span style="font-size: 18px; margin-right: 10px;">☕</span>
    <span>You can redeem this gift card at any Coffee Art Shop location or online. To redeem online, click the button below.</span>
</div>

@component('mail::button', ['url' => $redeemUrl, 'color' => 'success'])
Redeem Your Gift Card Now
@endcomponent

## How to redeem in-store
<div style="display: flex; align-items: center; margin-bottom: 15px;">
    <span style="font-size: 18px; margin-right: 10px;">📱</span>
    <span>Show the QR code below to the barista when ordering:</span>
</div>

<div style="text-align: center; margin: 25px 0;">
    <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ urlencode($redeemUrl) }}" alt="QR Code" style="max-width: 200px;">
</div>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center;">
        <span style="font-size: 18px; margin-right: 10px;">⭐</span>
        <span>Thank you for being part of the Coffee Art Shop experience!</span>
    </div>
</div>

Warmest Regards,<br>
{{ config('app.name') }} Team
@endcomponent
