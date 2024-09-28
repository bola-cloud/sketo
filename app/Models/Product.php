<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'category_id', 'cost_price', 'selling_price', 'quantity', 'barcode', 'barcode_path' , 'color','threshold','image'];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_products')
                    ->withPivot('quantity', 'cost_price')
                    ->withTimestamps();
    }

    // In app/Models/Product.php
    public function productTransfers()
    {
        return $this->hasMany(ProductTransfer::class);
    }
}
