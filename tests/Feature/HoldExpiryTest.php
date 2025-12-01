<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Hold;
use Illuminate\Support\Facades\Artisan;

class HoldExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_hold_expiry_releases_stock()
    {
        Product::create(['name'=>'p','stock'=>50,'price'=>10]);
        $hold = Hold::create([
            'product_id'=>1,
            'qty'=>5,
            'expires_at'=>now()->subMinutes(1),
            'used'=>false,
            'released'=>false
        ]);

        // run scheduler task once
        Artisan::call('schedule:run');

        $this->assertTrue($hold->fresh()->released);
        $this->assertEquals(50, Product::find(1)->stock);
    }
}
