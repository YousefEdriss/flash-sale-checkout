<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Hold;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate(['hold_id' => 'required|integer|exists:holds,id']);

        $order = DB::transaction(function () use ($data) {
            $hold = Hold::where('id', $data['hold_id'])->lockForUpdate()->firstOrFail();

            if (!$hold->isActive()) {
                abort(400, 'Hold is invalid, used, released, or expired');
            }

            $hold->markUsed();

            $order = Order::create([
                'hold_id' => $hold->id,
                'status' => 'pending',
                'payment_ref' => null
            ]);

            return $order;
        });

        return response()->json($order, 201);
    }
}
