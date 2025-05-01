<?php

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Models\Branch;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Branch::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Table::class)->nullable()->constrained()->nullOnDelete();
            $table->enum('status', array_column(OrderStatus::cases(), 'value'))
                  ->default(OrderStatus::PENDING->value);
            $table->enum('payment_status', array_column(PaymentStatus::cases(), 'value'))
                  ->default(PaymentStatus::PENDING->value);
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->text('special_instructions')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            // $table->softDeletes();

            // Index for frequent queries
            $table->index(['user_id', 'created_at']);
            $table->index(['branch_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
