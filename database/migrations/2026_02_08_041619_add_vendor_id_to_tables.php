<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'categories',
            'brands',
            'products',
            'clients',
            'suppliers',
            'invoices',
            'purchases',
            'sales',
            'purchase_products',
            'sales_installments',
            'purchase_installments',
            'shifts',
            'product_transfers',
            'customer_returns',
            'supplier_returns',
            'quantity_updates'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // vendor_id is nullable for now to support existing data
                // We will cascade on delete so if a vendor is deleted, all their data is gone
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'categories',
            'brands',
            'products',
            'clients',
            'suppliers',
            'invoices',
            'purchases',
            'sales',
            'purchase_products',
            'sales_installments',
            'purchase_installments',
            'shifts',
            'product_transfers',
            'customer_returns',
            'supplier_returns',
            'quantity_updates'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // Drop foreign key first, then column
                // Constraint name usually table_vendor_id_foreign
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            });
        }
    }
};
