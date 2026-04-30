<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Change quantity to decimal in products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        // Change quantity to decimal in sales
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        // Change quantity to decimal in purchase_products
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
            if (Schema::hasColumn('purchase_products', 'remaining_quantity')) {
                $table->decimal('remaining_quantity', 10, 3)->change();
            }
        });

        // Create Sub-Units table for Retail/Bulk relations
        Schema::create('product_sub_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('main_product_id'); // e.g. Box of Nescafe
            $table->unsignedBigInteger('sub_product_id');  // e.g. Sachet of Nescafe
            $table->decimal('conversion_factor', 10, 3);   // e.g. 12 (1 box = 12 sachets)
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('main_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sub_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_sub_units');
    }
};
