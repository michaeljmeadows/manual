<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function get(): Collection;
}
