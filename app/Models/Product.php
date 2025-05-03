<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'is_available',
        'image_url',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ingredients' => 'array',
        'nutritional_info' => 'array',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class)
            ->withPivot(['custom_price', 'discount_price'])
            ->withTimestamps();
    }

    public function product_options()
    {
        return $this->hasMany(ProductOption::class);
    }
}
