<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WebhookController extends Controller
{
    /**
     * Payment provider webhook:
     * payload:
     *  - order_id
     *  - idempotency_key (unique per payment)
     *  - status: success | failed
     */
    public function handle(Request $req)
    {
        $data = $req->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'idempotency_key' => 'required|string',
            'status' => 'required|in:success,failed'
        ]);

        $orderId = $data['order_id'];
        $key = $data['idempotency_key'];
        $status = $data['status'];

        // Idempotency handled by checking payment_ref and locking
        return DB::transaction(function () use ($orderId, $key, $status) {
            // Lock order row so concurrent webhooks are serialized
            $order = Order::where('id', $orderId)->lockForUpdate()->firstOrFail();

            // If this idempotency key was already used for another order -> reject
            if ($order->payment_ref && $order->payment_ref === $key) {
                return response()->json(['ok' => true]);
            }

            // If order already paid or cancelled with different key, decide behavior:
            if ($order->status === 'paid' && $order->payment_ref) {
                // Already paid
                return response()->json(['ok' => true]);
            }

            if ($status === 'success') {
                $order->status = 'paid';
                $order->payment_ref = $key;
                $order->save();
                return response()->json(['ok' => true]);
            } else {
                // Failed payment: cancel order and release hold stock
                $order->status = 'cancelled';
                $order->payment_ref = $key;
                $order->save();

                $hold = $order->hold;
                if ($hold && !$hold->released) {
                    $hold->release();
                }

                return response()->json(['ok' => true]);
            }
        });
    }
}
