<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'max_users',
        'max_products',
        'description',
        'is_active',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
