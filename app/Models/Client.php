<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = [
        'vendor_id',
        'name',
        'phone',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
