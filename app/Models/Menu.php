<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Menu extends Model
{
    protected $fillable = ['title', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['custom_price', 'discount_price'])
            ->withTimestamps();
    }

    public function getTotalPriceAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->custom_price
                ?? $product->pivot->discount_price
                ?? $product->price;
        });
    }

    protected static function booted()
    {
        self::saved(function ($menu) {
            if (request()->has('products')) {
                $products = collect(request('products'))->mapWithKeys(function ($item) {
                    return [$item['product_id'] => [
                        'custom_price' => $item['pivot']['custom_price'] ?? null,
                        'discount_price' => $item['pivot']['discount_price'] ?? null,
                    ]];
                });

                $menu->products()->sync($products);
            }
        });
    }
}
