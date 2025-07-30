<?php

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
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained()->onDelete('set null'); // Reference to original purchase
            $table->foreignId('purchase_product_id')->nullable()->constrained('purchase_products')->onDelete('set null'); // Reference to specific purchase product record
            $table->integer('quantity_returned'); // Quantity being returned
            $table->decimal('cost_price', 10, 2); // Cost price at time of return
            $table->string('reason')->nullable(); // Reason for return
            $table->text('notes')->nullable(); // Additional notes
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('returned_at')->nullable(); // When the return was processed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_returns');
    }
};
