<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubUnit extends Model
{
    use \App\Traits\BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'main_product_id',   // The Bulk Product (e.g., Box)
        'sub_product_id',    // The Retail Product (e.g., Sachet)
        'conversion_factor'  // How many sub-items in one main item (e.g., 12)
    ];

    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function subProduct()
    {
        return $this->belongsTo(Product::class, 'sub_product_id');
    }
}
