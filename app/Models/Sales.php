<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory, \App\Traits\BelongsToVendor;
    protected $fillable = ['vendor_id', 'product_id', 'quantity', 'total_price', 'invoice_id', 'purchase_product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function purchaseProduct()
    {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id');
    }

}
