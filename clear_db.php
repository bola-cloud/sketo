<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor_id = 4;
$purchases = \DB::table('purchases')->where('vendor_id', $vendor_id)->pluck('id');
$invoices = \DB::table('invoices')->where('vendor_id', $vendor_id)->pluck('id');
$products = \DB::table('products')->where('vendor_id', $vendor_id)->pluck('id');

\DB::table('product_transfers')->whereIn('old_purchase_id', $purchases)->delete();
\DB::table('purchase_installments')->whereIn('purchase_id', $purchases)->delete();
\DB::table('purchase_products')->whereIn('purchase_id', $purchases)->delete();
\DB::table('sales')->whereIn('invoice_id', $invoices)->delete();
\DB::table('invoices')->where('vendor_id', $vendor_id)->delete();
\DB::table('purchases')->where('vendor_id', $vendor_id)->delete();
\DB::table('quantity_updates')->whereIn('product_id', $products)->delete();
\DB::table('product_sub_units')->whereIn('main_product_id', $products)->delete();
\DB::table('products')->where('vendor_id', $vendor_id)->delete();
