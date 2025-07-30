<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_purchase_id',
        'new_purchase_id',
        'product_id',
        'transferred_quantity',
        'sold_quantity_old_purchase',
        'new_product_id'
    ];

    public function oldPurchase()
    {
        return $this->belongsTo(Purchase::class, 'old_purchase_id');
    }

    public function newPurchase()
    {
        return $this->belongsTo(Purchase::class, 'new_purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function newProduct()
    {
        return $this->belongsTo(Product::class, 'new_product_id');
    }

    // Accessor for formatted created_at
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    // Accessor for old invoice number
    public function getOldInvoiceNumberAttribute()
    {
        return $this->oldPurchase->invoice_number ?? 'غير محدد';
    }

    // Accessor for new invoice number
    public function getNewInvoiceNumberAttribute()
    {
        return $this->newPurchase->invoice_number ?? 'غير محدد';
    }

    // Accessor for old product name
    public function getOldProductNameAttribute()
    {
        return $this->product->name ?? 'غير محدد';
    }

    // Accessor for new product name
    public function getNewProductNameAttribute()
    {
        return $this->newProduct->name ?? 'غير محدد';
    }

    // Accessor for old cost price
    public function getOldCostPriceAttribute()
    {
        return $this->product->cost_price ?? 0;
    }

    // Accessor for old selling price
    public function getOldSellingPriceAttribute()
    {
        return $this->product->selling_price ?? 0;
    }

    // Accessor for new cost price
    public function getNewCostPriceAttribute()
    {
        return $this->newProduct->cost_price ?? 0;
    }

    // Accessor for new selling price
    public function getNewSellingPriceAttribute()
    {
        return $this->newProduct->selling_price ?? 0;
    }
}
