<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

abstract class RealDataSeedingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }
}
