<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'date_paid',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
