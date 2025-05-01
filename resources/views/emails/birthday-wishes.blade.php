@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <div style="font-size: 50px; margin-bottom: 10px;">🎂 🎉 🎈</div>
    <h1 style="margin: 0; color: #d97706; font-size: 28px;">Happy Birthday, {{ $userName }}!</h1>
</div>

<div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; margin-bottom: 10px;">
        <span style="font-size: 24px; margin-right: 10px;">🎁</span>
        <span style="font-size: 18px; font-weight: bold; color: #92400e;">Your Special Day, Our Treat!</span>
    </div>

    <p>All of us at {{ $appName }} wish you a fantastic birthday filled with joy, laughter, and of course, great coffee!</p>

    @if (!empty($specialOffer))
    <div style="display: flex; align-items: center; margin-top: 15px;">
        <span style="font-size: 24px; margin-right: 10px;">✨</span>
        <span style="font-weight: bold; color: #92400e;">{{ $specialOffer }}</span>
    </div>
    @endif
</div>

<div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="font-size: 24px; margin-right: 10px;">☕</span>
        <span style="font-size: 18px; font-weight: bold;">Birthday Recommendations</span>
    </div>

    <ul style="padding-left: 20px; margin: 0;">
        <li style="margin-bottom: 8px;">Try our signature <strong>Birthday Cake Latte</strong> - available only during your birthday week!</li>
        <li style="margin-bottom: 8px;">Pair it with our <strong>Celebration Pastry Box</strong> for the perfect birthday treat.</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    @component('mail::button', ['url' => url('/'), 'color' => 'success'])
    Visit Us Today
    @endcomponent
</div>

<div style="border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center;">
    <p style="font-size: 16px; margin-bottom: 10px;">🥳 Enjoy your special day! 🥳</p>
    <p style="color: #6b7280; font-size: 14px;">With love from the {{ $appName }} Team</p>
</div>

<div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
    <p>Follow us on social media for more updates and offers!</p>
    <div style="margin-top: 10px;">
        <span style="font-size: 20px; margin: 0 5px;">📱</span>
        <span style="font-size: 20px; margin: 0 5px;">💻</span>
        <span style="font-size: 20px; margin: 0 5px;">📷</span>
    </div>
</div>
@endcomponent
