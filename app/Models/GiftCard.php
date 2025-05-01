<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class GiftCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'recipient_email',
        'amount',
        'activation_key',
        'message',
        'occasion',
        'expires_at',
        'is_active',
    ];

    /**
     * Generate a unique activation key.
     */
    public static function generateUniqueActivationKey(): string
    {
        do {
            $key = Str::random(32);
        } while (self::where('activation_key', $key)->exists());

        return $key;
    }

    /**
     * Get the sender of the gift card.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the gift card.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Check if the gift card is redeemed.
     */
    public function isRedeemed(): bool
    {
        return $this->redeemed_at !== null;
    }

    /**
     * Check if the gift card is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if the gift card is valid (active, not redeemed, not expired).
     */
    public function isValid(): bool
    {
        return $this->is_active && ! $this->isRedeemed() && ! $this->isExpired();
    }

    /**
     * Redeem the gift card.
     */
    public function redeem(?User $user = null): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        $this->redeemed_at = now();

        if ($user && ! $this->recipient_id) {
            $this->recipient_id = $user->id;
        }

        return $this->save();
    }

    /**
     * Scope a query to only include active gift cards.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid gift cards.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNull('redeemed_at')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired gift cards.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to only include redeemed gift cards.
     */
    public function scopeRedeemed(Builder $query): Builder
    {
        return $query->whereNotNull('redeemed_at');
    }

    /**
     * Scope a query to find by activation key.
     */
    public function scopeByActivationKey(Builder $query, string $key): Builder
    {
        return $query->where('activation_key', $key);
    }

    /**
     * Scope a query to only include gift cards sent by a specific user.
     */
    public function scopeSentBy(Builder $query, User $user): Builder
    {
        return $query->where('sender_id', $user->id);
    }

    /**
     * Scope a query to only include gift cards received by a specific user.
     */
    public function scopeReceivedBy(Builder $query, User $user): Builder
    {
        return $query->where('recipient_id', $user->id)
            ->orWhere('recipient_email', $user->email);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (GiftCard $giftCard) {
            if (! $giftCard->activation_key) {
                $giftCard->activation_key = self::generateUniqueActivationKey();
            }

            if (! $giftCard->expires_at) {
                $giftCard->expires_at = now()->addYear();
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'redeemed_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
