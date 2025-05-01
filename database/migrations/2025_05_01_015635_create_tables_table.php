<?php

use App\Enum\TableStatus;
use App\Models\Branch;
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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained()->onDeleteCascade();
            $table->string('table_number');
            $table->string('qr_code')->unique();
            $table->integer('seating_capacity');
            $table->string('location')->nullable(); // e.g., "outdoor", "indoor-window"
            $table->enum('status', array_column(TableStatus::cases(), 'value'))
                  ->default(TableStatus::AVAILABLE->value);
            $table->timestamps();

            // Ensure table numbers are unique within a branch
            $table->unique(['branch_id', 'table_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
