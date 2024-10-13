<?php

namespace Tests\Unit\Rules;

use App\Models\Answer;
use App\Rules\AnswerExistsInCachedData;

class AnswerExistsInCachedDataTest extends QuestionAwareRuleTest
{
    public function test_rule_passes_when_answer_exists(): void
    {
        $answer = Answer::factory()->forQuestion()->forNextQuestion()->create();
        $rule = new AnswerExistsInCachedData($this->repository);
        $failed = false;

        $rule->validate('answers.0.answerId', $answer->id, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_rule_fails_when_answer_doesnt_exist(): void
    {
        $answer = Answer::factory()->forQuestion()->forNextQuestion()->create();
        $rule = new AnswerExistsInCachedData($this->repository);
        $failed = false;

        $rule->validate('answers.0.answerId', $answer->id + 1, static function () use (&$failed): void {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
