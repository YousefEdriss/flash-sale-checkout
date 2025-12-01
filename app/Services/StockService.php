<?php
namespace App\Services;
use App\Models\Product;
use App\Models\Hold;

class StockService
{
    // Returns effective available stock (product.stock - active holds)
    public function available(Product $product): int
    {
        $active = Hold::where('product_id', $product->id)
            ->where('expires_at', '>', now())
            ->where('released', false)
            ->sum('qty');
        return max(0, $product->stock - $active);
    }
}
