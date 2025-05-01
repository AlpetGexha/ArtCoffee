<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->onDeleteCascade();
            $table->string('option_category'); // e.g., "milk_type", "syrup_flavor"
            $table->string('option_name');     // e.g., "Soy Milk", "Vanilla"
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            // Ensure unique options per category for a product
            $table->unique(['product_id', 'option_category', 'option_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
