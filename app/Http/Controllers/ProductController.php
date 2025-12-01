<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Hold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // cached availability for short time (1s) to help under bursts
        $available = Cache::remember("product:{$id}:available", 1, function () use ($product) {
            $activeHolds = Hold::where('product_id', $product->id)
                ->where('expires_at', '>', now())
                ->where('released', false)
                ->sum('qty');
            return max(0, $product->stock - $activeHolds);
        });

        return response()->json([
            'id' => $product->id,
            'name'=> $product->name,
            'price'=> (float) $product->price,
            'available_stock' => (int) $available,
        ]);
    }
}
