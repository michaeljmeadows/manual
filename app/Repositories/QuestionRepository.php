<?php

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function get(): Collection
    {
        return Cache::remember(
            'questions',
            now()->endOfDay(),
            static function (): Collection {
                return Question::with([
                    'answers',
                    'answers.nextQuestion',
                    'answers.productsRecommended',
                    'answers.productsExcluded',
                ])->orderBy('number')->orderBy('part')->get();
            },
        );
    }
}
