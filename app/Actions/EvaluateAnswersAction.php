<?php

namespace App\Actions;

use App\Exceptions\UnknownQuestionAnswerException;
use App\Models\Answer;
use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Support\Collection;

class EvaluateAnswersAction implements EvaluateAnswersActionInterface
{
    public function __construct(protected QuestionRepositoryInterface $questionRepository) {}

    public function evaluate(array $data): Collection
    {
        $questions = $this->questionRepository->get()->keyBy('id');
        $answersLookup = $this->createAnswersLookup($data['answers']);

        $recommended = collect([]);
        $excluded = collect([]);

        $question = $questions->first();

        while ($question) {
            $answer = $this->getAnswerToQuestion($question, $answersLookup);

            $recommended = $recommended->merge($answer->productsRecommended)->unique();
            $excluded = $excluded->merge($answer->productsExcluded)->unique();

            $question = isset($questions[$answer->next_question_id])
                ? $questions[$answer->next_question_id]
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
