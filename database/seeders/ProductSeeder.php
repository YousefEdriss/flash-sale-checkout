<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Flash Sale Item',
            'stock' => 50,
            'price' => 199.99,
        ]);
    }
}
