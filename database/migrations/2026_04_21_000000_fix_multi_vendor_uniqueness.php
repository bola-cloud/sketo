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
        // Fix Brands
        Schema::table('brands', function (Blueprint $table) {
            // Drop old unique index if it exists
            // We use try-catch or check existence because database types differ (SQLite vs MySQL)
            try {
                $table->dropUnique(['name']);
            } catch (\Exception $e) {
                // Ignore if already dropped or different index name
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->unique(['vendor_id', 'name']);
        });

        // Fix Categories
        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropUnique(['name']);
            } catch (\Exception $e) {
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['vendor_id', 'name']);
        });

        // Fix Products Barcode
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropUnique(['barcode']);
            } catch (\Exception $e) {
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unique(['vendor_id', 'barcode']);
        });

        // Fix Invoices
        Schema::table('invoices', function (Blueprint $table) {
            try {
                $table->dropUnique(['invoice_code']);
            } catch (\Exception $e) {
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unique(['vendor_id', 'invoice_code']);
        });

        // Fix Purchases
        Schema::table('purchases', function (Blueprint $table) {
            try {
                $table->dropUnique(['invoice_number']);
            } catch (\Exception $e) {
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->unique(['vendor_id', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'name']);
            $table->unique('name');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'name']);
            $table->unique('name');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'barcode']);
            $table->unique('barcode');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'invoice_code']);
            $table->unique('invoice_code');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique(['vendor_id', 'invoice_number']);
            $table->unique('invoice_number');
        });
    }
};
