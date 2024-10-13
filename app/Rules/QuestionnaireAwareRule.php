<?php

namespace App\Rules;

use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class QuestionnaireAwareRule implements ValidationRule
{
    public function __construct(protected QuestionRepositoryInterface $questionRepository) {}
}
