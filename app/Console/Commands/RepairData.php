<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Purchase;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class RepairData extends Command
{
    protected $signature = 'repair:data';
    protected $description = 'Recalculate total amounts for all purchases and invoices based on their linked items.';

    public function handle()
    {
        $this->info('Starting data repair...');

        // 1. Repair Purchases
        $purchases = Purchase::all();
        $this->info("Found {$purchases->count()} purchases to check.");

        foreach ($purchases as $purchase) {
            $realTotal = DB::table('purchase_products')
                ->where('purchase_id', $purchase->id)
                ->sum(DB::raw('quantity * cost_price'));


            if ($purchase->total_amount != $realTotal) {
                $this->warn("Repairing Purchase ID: {$purchase->id}. Old Total: {$purchase->total_amount}, New Total: {$realTotal}");
                $purchase->update([
                    'total_amount' => $realTotal,
                    'change' => $realTotal - $purchase->paid_amount,
                ]);
            }

        }

        // 2. Repair Invoices
        $invoices = Invoice::with('sales')->get();
        $this->info("Found {$invoices->count()} invoices to check.");

        foreach ($invoices as $invoice) {
            $realSubtotal = $invoice->sales->sum('total_price');

            if ($invoice->subtotal != $realSubtotal) {
                $this->warn("Repairing Invoice ID: {$invoice->id}. Old Subtotal: {$invoice->subtotal}, New Subtotal: {$realSubtotal}");

                $totalAfterDiscount = $realSubtotal - $invoice->discount;
                $invoice->update([
                    'subtotal' => $realSubtotal,
                    'total_amount' => $totalAfterDiscount,
                    'change' => $totalAfterDiscount - $invoice->paid_amount,
                ]);
            }
        }

        $this->info('Data repair completed!');
    }
}
