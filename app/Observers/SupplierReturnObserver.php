<?php

namespace App\Observers;

use App\Models\SupplierReturn;

class SupplierReturnObserver
{
    public function created(SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status === 'completed' && $supplierReturn->product) {
            $supplierReturn->product->recalculateProductQuantity();
        }
    }

    public function updated(SupplierReturn $supplierReturn)
    {
        if ($supplierReturn->status === 'completed' && $supplierReturn->product) {
            $supplierReturn->product->recalculateProductQuantity();
        }
    }
}
