@component('mail::message')
# You've Received a Gift Card!

{{ $senderName }} has sent you a gift card worth **{{ $amount }}**.

@if($message)
## Personal Message
"{{ $message }}"
@endif

Your gift card is ready to be redeemed at Coffe Art Shop. This gift card is valid until {{ $expirationDate }}.

@component('mail::panel')
## Gift Card Details
**Amount:** {{ $amount }}
**Expiration:** {{ $expirationDate }}
**Activation Code:** {{ $giftCard->activation_key }}
@endcomponent

@component('mail::button', ['url' => $redeemUrl, 'color' => 'success'])
Redeem Now
@endcomponent

## How to Redeem
1. Create an account or log in at [CoffeArtShop.com]({{ route('home') }})
2. Go to the "Redeem Gift Card" page
3. Enter your activation code
4. The gift card amount will be added to your account balance

You can use your balance to purchase any of our delicious coffee or artisanal food items.

Thanks,<br>
{{ config('app.name') }} Team

<small>This gift card is subject to our terms and conditions. It cannot be exchanged for cash and is valid for a single use only.</small>
@endcomponent
