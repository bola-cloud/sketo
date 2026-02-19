<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
class SanityTest extends TestCase
{
    use RefreshDatabase;
    public function test_sanity()
    {
        $reflector = new \ReflectionClass(\App\Models\Product::class);
        echo "Product loaded from: " . $reflector->getFileName() . "\n";

        $user = User::factory()->create();
        \App\Models\Product::create([
            'name' => 'Test',
            'barcode' => '123456',
            'selling_price' => 100,
            'quantity' => 1,
            'cost_price' => 50
        ]);
        $this->assertNotNull($user);
    }
}
