<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Tests\TestCase;

class ProductRepositoryInterfaceTest extends TestCase
{
    protected ProductRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ProductRepositoryInterface::class);
    }

    public function test_repository_returns_all_products(): void
    {
        $targets = Product::factory()->count(3)->create();

        $products = $this->repository->get();

        $this->assertEquals($targets->count(), $products->count());
        foreach ($targets as $target) {
            $this->assertTrue($products->contains($target));
        }
    }
}
