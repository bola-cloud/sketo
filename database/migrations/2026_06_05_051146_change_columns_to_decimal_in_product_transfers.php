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
        Schema::table('product_transfers', function (Blueprint $table) {
            $table->decimal('quantity_before_transfer', 10, 3)->nullable()->change();
            $table->decimal('sold_quantity_old_purchase', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_transfers', function (Blueprint $table) {
            $table->integer('quantity_before_transfer')->nullable()->change();
            $table->integer('sold_quantity_old_purchase')->change();
        });
    }
};
