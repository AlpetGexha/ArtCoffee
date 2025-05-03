<?php

use App\Livewire\GiftCard\RedeemGiftCard;
use App\Livewire\GiftCard\SendGiftCard;
use App\Livewire\Order\OrderPage;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
Route::get('/welcome',function(){
    return view('welcome');
});
Route::get('/', function () {
    return redirect('order');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('order', OrderPage::class)->name('order');

    // Order Tracking Routes
    Route::get('orders/track/{orderId?}', App\Livewire\Order\OrderTrackingPage::class)->name('orders.track');
    Route::get('orders/history', App\Livewire\Order\OrderHistoryPage::class)->name('orders.history');

    // Gift Card Routes
    Route::get('gift-cards/send', SendGiftCard::class)->name('gift-cards.send');
    Route::get('gift-cards/redeem/{code?}', RedeemGiftCard::class)->name('gift-cards.redeem');
});

require __DIR__ . '/auth.php';
