<?php

namespace App\Observers;

use App\Models\CustomerReturn;

class CustomerReturnObserver
{
    public function created(CustomerReturn $customerReturn)
    {
        if ($customerReturn->status === 'completed' && $customerReturn->product) {
            $customerReturn->product->recalculateProductQuantity();
        }
    }

    public function updated(CustomerReturn $customerReturn)
    {
        if ($customerReturn->status === 'completed' && $customerReturn->product) {
            $customerReturn->product->recalculateProductQuantity();
        }
    }
}
