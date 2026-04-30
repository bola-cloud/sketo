<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Change quantity_returned to decimal in customer_returns
        Schema::table('customer_returns', function (Blueprint $table) {
            $table->decimal('quantity_returned', 10, 3)->change();
        });

        // Change quantity_returned to decimal in supplier_returns
        Schema::table('supplier_returns', function (Blueprint $table) {
            $table->decimal('quantity_returned', 10, 3)->change();
        });
        
        // Ensure quantity in product_transfers is also decimal (just in case)
        if (Schema::hasColumn('product_transfers', 'transferred_quantity')) {
            Schema::table('product_transfers', function (Blueprint $table) {
                $table->decimal('transferred_quantity', 10, 3)->change();
            });
        }
    }

    public function down()
    {
        Schema::table('customer_returns', function (Blueprint $table) {
            $table->integer('quantity_returned')->change();
        });

        Schema::table('supplier_returns', function (Blueprint $table) {
            $table->integer('quantity_returned')->change();
        });
    }
};
