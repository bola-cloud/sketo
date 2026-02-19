<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierSyncTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $vendor;

    protected function setUp(): void
    {
        try {
            parent::setUp();

            // 1. Create User first (vendor_id is nullable) manually to avoid factory issues
            $this->user = User::create([
                'name' => 'Sync Tester',
                'email' => 'tester@sketo.com',
                'password' => bcrypt('password')
            ]);

            // 2. Create Vendor with this user as owner
            $this->vendor = \App\Models\Vendor::create([
                'business_name' => 'Sync Test Shop',
                'owner_id' => $this->user->id
            ]);

            // 3. Update User with vendor_id
            $this->user->update(['vendor_id' => $this->vendor->id]);

            $this->actingAs($this->user);

            Shift::create([
                'vendor_id' => $this->vendor->id,
                'user_id' => $this->user->id,
                'status' => 'open',
                'starting_cash' => 1000,
                'start_time' => now(),
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n" . $e->getTraceAsString();
            throw $e;
        } catch (\Error $e) {
            echo $e->getMessage() . "\n" . $e->getTraceAsString();
            throw $e;
        }
    }

    public function test_can_sync_offline_invoice()
    {
        // 0. Create Supplier
        $supplier = \App\Models\Supplier::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Test Supplier'
        ]);

        // 1. Create a product with stock manually
        $product = Product::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Test Product',
            'barcode' => 'TEST123',
            'selling_price' => 100,
            'quantity' => 10,
            'cost_price' => 50
        ]);

        // 2. Create a purchase batch for FIFO
        $purchase = Purchase::create([
            'vendor_id' => $this->vendor->id,
            'supplier_id' => $supplier->id,
            'total_amount' => 1000,
            'user_id' => $this->user->id
        ]);

        PurchaseProduct::create([
            'vendor_id' => $this->vendor->id,
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'remaining_quantity' => 10,
            'purchase_price' => 50,
            'selling_price' => 100
        ]);

        $uuid = 'INV-OFFLINE-' . uniqid();
        $payload = [
            'uuid' => $uuid,
            'data' => [
                'cart' => [
                    'TEST123' => [
                        'price' => 100,
                        'quantity' => 2,
                        'name' => 'Test Product'
                    ]
                ],
                'paid_amount' => 200,
                'discount' => 0,
                'client_id' => null,
                'user_id' => $this->user->id
            ]
        ];

        // 3. Sync the invoice
        $response = $this->postJson(route('cashier.syncOffline'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Synced successfully']);

        // 4. Verify database
        $this->assertDatabaseHas('invoices', [
            'invoice_code' => $uuid,
            'total_amount' => 200,
            'paid_amount' => 200
        ]);

        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // 5. Verify inventory reduction
        $this->assertEquals(8, $product->fresh()->quantity);
    }

    public function test_sync_is_idempotent()
    {
        $supplier = \App\Models\Supplier::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Repeat Test Supplier'
        ]);

        $product = Product::create([
            'vendor_id' => $this->vendor->id,
            'name' => 'Repeat Product',
            'barcode' => 'TEST456',
            'selling_price' => 100,
            'quantity' => 10,
            'cost_price' => 50
        ]);

        $purchase = Purchase::create([
            'vendor_id' => $this->vendor->id,
            'supplier_id' => $supplier->id,
            'total_amount' => 1000,
            'user_id' => $this->user->id
        ]);

        PurchaseProduct::create([
            'vendor_id' => $this->vendor->id,
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'remaining_quantity' => 10,
            'purchase_price' => 50,
            'selling_price' => 100
        ]);

        $uuid = 'INV-REPEAT-123';
        $payload = [
            'uuid' => $uuid,
            'data' => [
                'cart' => [
                    'TEST456' => [
                        'price' => 100,
                        'quantity' => 1,
                        'name' => 'Repeat Product'
                    ]
                ],
                'paid_amount' => 100
            ]
        ];

        // Sync first time
        $this->postJson(route('cashier.syncOffline'), $payload)->assertStatus(200);
        $this->assertEquals(9, $product->fresh()->quantity);

        // Sync second time (same UUID)
        $response = $this->postJson(route('cashier.syncOffline'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Already synced']);

        // Inventory should still be 9, not 8
        $this->assertEquals(9, $product->fresh()->quantity);
    }
}
