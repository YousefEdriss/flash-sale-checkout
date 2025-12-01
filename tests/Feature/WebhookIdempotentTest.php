<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Hold;
use App\Models\Order;

class WebhookIdempotentTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_is_idempotent_and_out_of_order_safe()
    {
        Product::create(['name'=>'p','stock'=>10,'price'=>10]);
        $hold = Hold::create(['product_id'=>1,'qty'=>2,'expires_at'=>now()->addMinutes(2),'used'=>false,'released'=>false]);

        // Simulate order creation that may happen after webhook
        $order = Order::create(['hold_id'=>$hold->id,'status'=>'pending']);

        // Send webhook twice
        $payload = ['order_id'=>$order->id, 'idempotency_key'=>'key123', 'status'=>'success'];

        $this->postJson('/api/payments/webhook', $payload)->assertStatus(200);
        $this->postJson('/api/payments/webhook', $payload)->assertStatus(200);

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('key123', $order->fresh()->payment_ref);
    }
}
