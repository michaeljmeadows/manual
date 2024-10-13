<?php

namespace Tests\Unit\Rules;

use App\Models\Answer;
use App\Models\Question;
use App\Rules\AnswerBelongsToQuestion;

class AnswerBelongsToQuestionTest extends QuestionAwareRuleTest
{
    public function test_rule_passes_when_answer_belongs_to_question(): void
    {
        $answer = Answer::factory()->forQuestion()->forNextQuestion()->create();
        $rule = new AnswerBelongsToQuestion($this->repository);
        $rule->setData([
            'answers' => [
                ['questionId' => $answer->question->id, 'answerId' => $answer->id],
            ],
        ]);
        $failed = false;

        $rule->validate('answers.0.answerId', $answer->id, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_rule_fails_when_answer_doesnt_belong_to_question(): void
    {
        $answer = Answer::factory()->forQuestion()->forNextQuestion()->create();
        $question = Question::factory()->create();
        $rule = new AnswerBelongsToQuestion($this->repository);
        $rule->setData([
            'answers' => [
                ['questionId' => $question->id, 'answerId' => $answer->id],
            ],
        ]);
        $failed = false;

        $rule->validate('answers.0.answerId', $answer->id + 1, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
