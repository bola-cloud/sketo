<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToVendor;

    protected $fillable = ['vendor_id', 'name', 'category_id', 'brand_id', 'cost_price', 'selling_price', 'quantity', 'barcode', 'barcode_path', 'color', 'threshold', 'image'];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_products')
            ->withPivot('quantity', 'cost_price', 'id')
            ->withTimestamps();
    }

    // In app/Models/Product.php
    public function productTransfers()
    {
        return $this->hasMany(ProductTransfer::class);
    }

    public function supplierReturns()
    {
        return $this->hasMany(SupplierReturn::class);
    }

    public function purchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::class);
    }

    // Get available stock grouped by supplier
    public function getStockBySupplier()
    {
        return $this->purchaseProducts()
            ->with(['purchase.supplier'])
            ->where('remaining_quantity', '>', 0)
            ->get()
            ->groupBy(function ($item) {
                return $item->purchase->supplier_id;
            });
    }

    // Get the oldest available stock (FIFO)
    public function getOldestAvailableStock($quantity = null)
    {
        $query = $this->purchaseProducts()
            ->with(['purchase.supplier'])
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at', 'asc');

        if ($quantity) {
            return $query->get()->filter(function ($item) use ($quantity) {
                return $item->remaining_quantity >= $quantity;
            });
        }

        return $query->get();
    }

    // Calculate total available quantity
    public function getTotalAvailableQuantityAttribute()
    {
        return $this->purchaseProducts()->sum('remaining_quantity');
    }

    // Get available stock from a specific supplier
    public function getStockFromSupplier($supplierId)
    {
        return $this->purchaseProducts()
            ->whereHas('purchase', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // Check if this is a transferred product
    public function getIsTransferredAttribute()
    {
        return str_contains($this->color, '-منقول') || str_contains($this->barcode, 'TRF-');
    }

    // Get transfer history for this product
    public function transferHistory()
    {
        return $this->hasMany(ProductTransfer::class, 'product_id');
    }

    // Get transfers where this product was created as a new product
    public function transfersAsNewProduct()
    {
        return $this->hasMany(ProductTransfer::class, 'new_product_id');
    }

    // Get the original product if this is a transferred product
    public function getOriginalProductAttribute()
    {
        if ($this->is_transferred) {
            $transfer = $this->transfersAsNewProduct()->first();
            return $transfer ? $transfer->product : null;
        }
        return null;
    }
    public function recalculateProductQuantity()
    {
        $availableQuantity = $this->purchaseProducts()->sum('remaining_quantity');
        $oldQuantity = $this->quantity;
        $this->update(['quantity' => max($availableQuantity, 0)]);
        \Log::info('Recalculated product quantity (batch sum)', [
            'product_id' => $this->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => max($availableQuantity, 0),
            'called_from' => 'Product::recalculateProductQuantity',
        ]);
    }
}
