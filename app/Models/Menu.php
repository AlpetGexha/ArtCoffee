<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $fillable = ['title', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['custom_price', 'discount_price'])
            ->withTimestamps();
    }
}
