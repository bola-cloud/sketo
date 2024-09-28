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
        Schema::create('product_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_purchase_id');
            $table->unsignedBigInteger('new_purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('transferred_quantity');
            $table->integer('sold_quantity_old_purchase');
            $table->timestamps();

            $table->foreign('old_purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('new_purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('new_product_id');
            $table->foreign('new_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transfers');
    }
};
