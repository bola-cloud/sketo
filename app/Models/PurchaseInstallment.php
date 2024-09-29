<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'amount_paid',
        'date_paid',
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}
