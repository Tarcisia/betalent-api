<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Produto A', 'amount' => 1000],
            ['name' => 'Produto B', 'amount' => 2500],
            ['name' => 'Produto C', 'amount' => 5000],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['name' => $product['name']],
                ['amount' => $product['amount']]
            );
        }
    }
}
