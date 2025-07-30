<?php

namespace App\Observers;

use App\Models\PurchaseProduct;

class PurchaseProductObserver
{
    /**
     * Handle the PurchaseProduct "created" event.
     */
    public function created(PurchaseProduct $purchaseProduct): void
    {
        //
    }

    /**
     * Handle the PurchaseProduct "updated" event.
     */
    public function updated(PurchaseProduct $purchaseProduct): void
    {
        //
    }

    /**
     * Handle the PurchaseProduct "deleted" event.
     */
    public function deleted(PurchaseProduct $purchaseProduct): void
    {
        //
    }

    /**
     * Handle the PurchaseProduct "restored" event.
     */
    public function restored(PurchaseProduct $purchaseProduct): void
    {
        //
    }

    /**
     * Handle the PurchaseProduct "force deleted" event.
     */
    public function forceDeleted(PurchaseProduct $purchaseProduct): void
    {
        //
    }
}
