<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['buyer_name', 'buyer_phone', 'invoice_code', 'subtotal', 'discount', 'total_amount', 'paid_amount', 'change', 'user_id','client_id'];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(SalesInstallment::class);
    }

    // Method to calculate the total paid so far
    public function getTotalPaidAttribute()
    {
        return $this->installments()->sum('amount_paid');
    }

    // Method to calculate the remaining amount (change)
    public function getChangeAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function returns()
    {
        return $this->hasMany(CustomerReturn::class);
    }

    // Method to get total return amount
    public function getTotalReturnsAttribute()
    {
        return $this->returns()->sum('return_amount');
    }

    // Method to check if invoice has returns
    public function hasReturns()
    {
        return $this->returns()->exists();
    }
}
