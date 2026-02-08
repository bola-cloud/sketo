<?php

namespace App\Traits;

use App\Models\Vendor;
use App\Scopes\VendorScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToVendor
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootBelongsToVendor()
    {
        static::addGlobalScope(new VendorScope);

        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->vendor_id) {
                $model->vendor_id = Auth::user()->vendor_id;
            }
        });
    }

    /**
     * Get the vendor that owns the model.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
