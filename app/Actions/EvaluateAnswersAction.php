<?php

namespace App\Actions;

use App\Exceptions\UnknownQuestionAnswerException;
use App\Models\Answer;
use App\Models\Question;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Support\Collection;

class EvaluateAnswersAction implements EvaluateAnswersActionInterface
{
    protected Collection $products;

    protected Collection $questions;

    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected QuestionRepositoryInterface $questionRepository
    ) {
        $this->products = $this->productRepository->get()->keyBy('id');
        $this->questions = $this->questionRepository->get()->keyBy('id');
    }

    public function evaluate(array $data): Collection
    {
        $answersLookup = $this->createAnswersLookup($data['answers']);

        $recommended = collect([]);
        $excluded = collect([]);

        $question = $this->questions->first();

        while ($question) {
            $answer = $this->getAnswerToQuestion($question, $answersLookup);

            $recommended = $recommended->merge($answer->productsRecommended)->unique();
            $excluded = $excluded->merge($answer->productsExcluded)->unique();

            $question = isset($this->questions[$answer->next_question_id])
                ? $this->questions[$answer->next_question_id]
                : null;
        }

        return $recommended->whereNotIn('id', $excluded->pluck('id'));
    }

    protected function createAnswersLookup(array $answers): array
    {
        return array_combine(
            array_column($answers, 'questionId'),
            array_column($answers, 'answerId'),
        );
    }

    protected function getAnswerToQuestion(Question $question, array $answersLookup): Answer
    {
        $answerId = $answersLookup[$question->id] ?? null;

        throw_unless($answerId, UnknownQuestionAnswerException::class, 'Question '.$question->number.' not answered.');

        $answer = $question->answers->firstWhere('id', $answerId);

        throw_unless($answer, UnknownQuestionAnswerException::class, 'Unknown answer to question '.$question->number);

        return $answer;
    }
}
