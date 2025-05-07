<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    return (int) $user->id === (int) $orderId;
});

Broadcast::channel('orders', function ($user) {
    return (int) $user->id === (int) $user->id;
});

Broadcast::channel('private:orders', function ($user) {
    return (int) $user->id === (int) $user->id;
});

// auth
Broadcast::channel('auth', function () {
    return true;
});

Broadcast::channel('private:orders.{orderId}', function ($user, $orderId) {
    return (int) $user->id === (int) $orderId;
});
