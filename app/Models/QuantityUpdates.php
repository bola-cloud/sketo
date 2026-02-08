<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuantityUpdates extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = ['vendor_id', 'product_id', 'old_quantity', 'new_quantity', 'user_id', 'action'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed the quantity update.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
