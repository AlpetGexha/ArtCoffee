<?php

use App\Enum\ProductCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->enum('category', array_column(ProductCategory::cases(), 'value'));
            $table->boolean('is_customizable')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time_minutes')->default(5);
            $table->integer('loyalpoints_per_item')->default(0);
            $table->json('ingredients')->nullable();
            $table->json('nutritional_info')->nullable();
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
