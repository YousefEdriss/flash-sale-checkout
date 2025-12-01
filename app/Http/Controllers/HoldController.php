<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Hold;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HoldController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = (int) $data['qty'];

        // Use DB transaction with row lock to avoid oversell
        $hold = DB::transaction(function () use ($product, $qty) {
            $locked = Product::where('id', $product->id)->lockForUpdate()->first();
            if ($locked->stock < $qty) {
                throw ValidationException::withMessages(['qty' => 'Not enough stock']);
            }

            $locked->decrement('stock', $qty);

            $hold = Hold::create([
                'product_id' => $product->id,
                'qty' => $qty,
                'expires_at' => now()->addMinutes(2),
                'used' => false,
                'released' => false
            ]);

            return $hold;
        });

        return response()->json([
            'hold_id' => $hold->id,
            'expires_at' => $hold->expires_at->toDateTimeString()
        ], 201);
    }
}
