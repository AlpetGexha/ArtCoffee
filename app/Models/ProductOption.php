<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProductOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'option_category',
        'option_name',
        'additional_price',
        'is_available',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_price' => 'decimal:2',
        'is_available' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the product that owns the option.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order item customizations for this option.
     */
    public function orderItemCustomizations(): HasMany
    {
        return $this->hasMany(OrderItemCustomization::class);
    }

    /**
     * Scope a query to only include available options.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to filter options by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('option_category', $category);
    }

    // add name atribute
    public function getNameAttribute(): string
    {
        return $this->option_name;
    }
}
