<?php

use App\Models\OrderItem;
use App\Models\ProductOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderItem::class)->constrained()->onDeleteCascade();
            $table->foreignIdFor(ProductOption::class)->constrained()->restrictOnDelete();
            $table->decimal('option_price', 10, 2);
            $table->timestamps();

            // Prevent duplicate options on an order item
            $table->unique(['order_item_id', 'product_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_customizations');
    }
};
