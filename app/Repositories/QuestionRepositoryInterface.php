<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface QuestionRepositoryInterface
{
    public function get(): Collection;
}
