@component('mail::message')

# 🎂 🎉 🎈
## Happy Birthday, {{ $userName }}!

---

### 🎁 Your Special Day, Our Treat!

All of us at **{{ $appName }}** wish you a fantastic birthday filled with joy, laughter, and of course, great coffee!

@if (!empty($specialOffer))
✨ **{{ $specialOffer }}**
@endif

---

### ☕ Birthday Recommendations

- Try our signature **Birthday Cake Latte** – available only during your birthday week!
- Pair it with our **Celebration Pastry Box** for the perfect birthday treat.

@component('mail::button', ['url' => url('/'), 'color' => 'success'])
Visit Us Today
@endcomponent

---

🥳 **Enjoy your special day!** 🥳
With love from the **{{ $appName }}** Team

---

Follow us on social media for more updates and offers!
📱 💻 📷

@endcomponent
