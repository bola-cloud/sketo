<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_number', 'type', 'total_amount', 'description','paid_amount','change'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_products')
                    ->withPivot('quantity', 'cost_price')
                    ->withTimestamps();
    }

    // In app/Models/Purchase.php

    public function productTransfersAsOld()
    {
        return $this->hasMany(ProductTransfer::class, 'old_purchase_id');
    }

    public function productTransfersAsNew()
    {
        return $this->hasMany(ProductTransfer::class, 'new_purchase_id');
    }
}
