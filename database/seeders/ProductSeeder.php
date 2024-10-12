<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /** @var array */
    protected const PRODUCTS = [
        [
            'reference' => 'sildenafil_50',
            'name' => 'Sildenafil',
            'dosage_mg' => 50,
        ],
        [
            'reference' => 'sildenafil_100',
            'name' => 'Sildenafil',
            'dosage_mg' => 100,
        ],
        [
            'reference' => 'tadalafil_10',
            'name' => 'Tadalafil',
            'dosage_mg' => 10,
        ],
        [
            'reference' => 'tadalafil_20',
            'name' => 'Tadalafil',
            'dosage_mg' => 20,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::PRODUCTS as $product) {
            Product::create($product);
        }
    }
}
