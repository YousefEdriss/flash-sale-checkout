<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ParallelTesting;

class StockConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_oversell_under_parallel_holds()
    {
        // seed product with stock 5
        Product::create(['name'=>'p','stock'=>5,'price'=>10]);

        $responses = [];
        $attempts = 10;
        for ($i=0;$i<$attempts;$i++){
            $responses[] = $this->postJson('/api/holds', ['product_id'=>1,'qty'=>1]);
        }

        // count successful holds
        $success = 0;
        foreach ($responses as $r) {
            if ($r->status() === 201) $success++;
        }

        $this->assertLessThanOrEqual(5, $success);
        $this->assertEquals(5, \App\Models\Hold::count());
    }
}
