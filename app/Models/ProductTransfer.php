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
}
