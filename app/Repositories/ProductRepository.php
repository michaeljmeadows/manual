<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function get(): Collection
    {
        return Cache::remember(
            'products',
            now()->endOfDay(),
            static fn (): Collection => Product::orderBy('id')->get(),
        );
    }
}
