<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing purchase_products to set remaining_quantity equal to quantity
        // This is needed for the FIFO system to work correctly
        DB::statement('UPDATE purchase_products SET remaining_quantity = quantity WHERE remaining_quantity = 0');

        // For records that might have some sales already, we need to calculate the remaining quantity
        // by subtracting the sold quantities from the original quantity
        DB::statement('
            UPDATE purchase_products pp
            LEFT JOIN (
                SELECT purchase_product_id, SUM(quantity) as sold_quantity
                FROM sales
                WHERE purchase_product_id IS NOT NULL
                GROUP BY purchase_product_id
            ) s ON pp.id = s.purchase_product_id
            SET pp.remaining_quantity = pp.quantity - COALESCE(s.sold_quantity, 0)
            WHERE pp.remaining_quantity != pp.quantity - COALESCE(s.sold_quantity, 0)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset remaining_quantity to 0
        DB::statement('UPDATE purchase_products SET remaining_quantity = 0');
    }
};
