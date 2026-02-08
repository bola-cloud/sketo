<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'name',
        'phone',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function supplierReturns()
    {
        return $this->hasMany(SupplierReturn::class);
    }
}
