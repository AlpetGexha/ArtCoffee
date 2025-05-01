<?php

namespace App\Livewire\Actions\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ProcessWalletPaymentAction
{
    /**
     * Process a wallet payment for an order.
     *
     * @param User $user The user making the payment
     * @param Order $order The order being paid for
     * @param float $amount The amount to charge
     * @return bool Whether the payment was successful
     */
    public function handle(User $user, Order $order, float $amount): bool
    {
        // Check if user has enough balance
        if ($user->balanceFloat < $amount) {
            Log::warning("Wallet payment failed: Insufficient funds", [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'available_balance' => $user->balanceFloat,
            ]);

            return false;
        }

        try {
            // Use a transaction to ensure data integrity
            return DB::transaction(function () use ($user, $order, $amount) {
                // Withdraw from wallet
                $user->withdrawFloat($amount, [
                    'description' => "Payment for Order #{$order->id}",
                    'order_id' => $order->id,
                    'type' => 'order_payment',
                ]);

                // Update order metadata with payment information
                $order->update([
                    'payment_transaction_id' => $user->wallet->transactions()->latest()->first()->id ?? null,
                    'payment_processed_at' => now(),
                ]);

                Log::info("Wallet payment successful", [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'transaction_id' => $user->wallet->transactions()->latest()->first()->id ?? null,
                ]);

                return true;
            });
        } catch (\Exception $e) {
            Log::error("Wallet payment failed: {$e->getMessage()}", [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'exception' => $e,
            ]);

            return false;
        }
    }
}
