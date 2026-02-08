<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = ['vendor_id', 'name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
