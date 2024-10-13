<?php

namespace App\Actions;

use Illuminate\Support\Collection;

interface EvaluateAnswersActionInterface
{
    public function evaluate(array $data): Collection;
}
