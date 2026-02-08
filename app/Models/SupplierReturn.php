<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReturn extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'product_id',
        'supplier_id',
        'purchase_id',
        'purchase_product_id',
        'quantity_returned',
        'cost_price',
        'reason',
        'notes',
        'status',
        'returned_at'
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'cost_price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseProduct()
    {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id');
    }

    // Calculate total return value
    public function getTotalValueAttribute()
    {
        return $this->quantity_returned * $this->cost_price;
    }

    // Scope for completed returns
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope for pending returns
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
