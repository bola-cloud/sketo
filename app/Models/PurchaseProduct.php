<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'cost_price', 'remaining_quantity'];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set remaining_quantity when creating a new purchase product
        static::creating(function ($purchaseProduct) {
            if (empty($purchaseProduct->remaining_quantity)) {
                $purchaseProduct->remaining_quantity = $purchaseProduct->quantity;
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplierReturns()
    {
        return $this->hasMany(SupplierReturn::class, 'purchase_product_id');
    }

    // Calculate how much quantity has been sold
    public function getSoldQuantityAttribute()
    {
        return $this->quantity - $this->remaining_quantity;
    }

    // Check if this batch is fully depleted
    public function getIsDepletedAttribute()
    {
        return $this->remaining_quantity <= 0;
    }

    // Check if this batch has enough stock for a given quantity
    public function hasStock($quantity)
    {
        return $this->remaining_quantity >= $quantity;
    }

    // Reduce remaining quantity (for sales/returns)
    public function reduceStock($quantity)
    {
        if ($this->hasStock($quantity)) {
            $this->decrement('remaining_quantity', $quantity);
            return true;
        }
        return false;
    }

    // Restore remaining quantity (for return cancellations)
    public function restoreStock($quantity)
    {
        $maxRestore = $this->quantity - $this->remaining_quantity;
        $actualRestore = min($quantity, $maxRestore);
        $this->increment('remaining_quantity', $actualRestore);
        return $actualRestore;
    }

    // Get supplier from purchase
    public function getSupplierAttribute()
    {
        return $this->purchase->supplier;
    }
}
