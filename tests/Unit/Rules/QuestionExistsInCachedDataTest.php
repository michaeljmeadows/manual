<?php

namespace Tests\Unit\Rules;

use App\Models\Question;
use App\Rules\QuestionExistsInCachedData;

class QuestionExistsInCachedDataTest extends QuestionAwareRuleTest
{
    public function test_rule_passes_when_question_exists(): void
    {
        $question = Question::factory()->create();
        $rule = new QuestionExistsInCachedData($this->repository);
        $failed = false;

        $rule->validate('answers.0.questionId', $question->id, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_rule_fails_when_question_doesnt_exist(): void
    {
        $question = Question::factory()->create();
        $rule = new QuestionExistsInCachedData($this->repository);
        $failed = false;

        $rule->validate('answers.0.questionId', $question->id + 1, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
