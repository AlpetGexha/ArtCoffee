@component('mail::message')
# You've Received a Coffee Art Shop Gift Card!

<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('images/gift-card.png') }}" alt="Gift Card" style="max-width: 300px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
</div>

## Hello there!

**{{ $senderName }}** has sent you a gift card worth **{{ $amount }}**!

@if(!empty($message))
## Personal Message:
> "{{ $message }}"
@endif

@component('mail::panel')
## Gift Card Details
- **Amount:** {{ $amount }}
- **Occasion:** {{ $giftCard->occasion ?? 'Gift' }}
- **Expires On:** {{ $expirationDate }}
@endcomponent

You can redeem this gift card at any Coffee Art Shop location or online. To redeem online, click the button below.

@component('mail::button', ['url' => $redeemUrl, 'color' => 'success'])
Redeem Your Gift Card Now
@endcomponent

## How to redeem in-store
Show the QR code below to the barista when ordering:

<div style="text-align: center; margin: 25px 0;">
    <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ urlencode($redeemUrl) }}" alt="QR Code" style="max-width: 200px;">
</div>

Thank you for being part of the Coffee Art Shop experience!

Warmest Regards,<br>
{{ config('app.name') }} Team
@endcomponent
