<?php

namespace Tests\Unit\Actions;

use App\Actions\EvaluateAnswersActionInterface;
use App\Models\Answer;
use App\Models\Product;
use App\Models\Question;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EvaluateAnswersActionInterfaceTest extends TestCase
{
    protected EvaluateAnswersActionInterface $action;

    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->action = app(EvaluateAnswersActionInterface::class);
    }

    public function test_action_can_process_answers(): void
    {
        $question3 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create(['number' => 3]);
        $question2 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question3->id]))
            ->create(['number' => 2]);
        $question1 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question2->id]))
            ->create(['number' => 2]);

        $products = $this->action->evaluate([
            'answers' => [
                ['questionId' => $question1->id, 'answerId' => $question1->answers->first()->id],
                ['questionId' => $question2->id, 'answerId' => $question2->answers->first()->id],
                ['questionId' => $question3->id, 'answerId' => $question3->answers->first()->id],
            ],
        ]);

        $this->assertTrue($products->isEmpty());
    }

    public function test_action_can_recommend_products(): void
    {
        $product = Product::factory()->create();
        $question3 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create(['number' => 3]);
        $question2 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question3->id]))
            ->create(['number' => 2]);
        $question1 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question2->id]))
            ->create(['number' => 1]);
        $question2->answers->first()->productsRecommended()->attach($product);

        $products = $this->action->evaluate([
            'answers' => [
                ['questionId' => $question1->id, 'answerId' => $question1->answers->first()->id],
                ['questionId' => $question2->id, 'answerId' => $question2->answers->first()->id],
                ['questionId' => $question3->id, 'answerId' => $question3->answers->first()->id],
            ],
        ]);

        $this->assertEquals(1, $products->count());
        $this->assertEquals($product->id, $products->first()->id);
    }

    public function test_action_can_exclude_products(): void
    {
        $product = Product::factory()->create();
        $question3 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create(['number' => 3]);
        $question2 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question3->id]))
            ->create(['number' => 2]);
        $question1 = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => $question2->id]))
            ->create(['number' => 1]);
        $question2->answers->first()->productsRecommended()->attach($product);
        $question3->answers->first()->productsExcluded()->attach($product);

        $products = $this->action->evaluate([
            'answers' => [
                ['questionId' => $question1->id, 'answerId' => $question1->answers->first()->id],
                ['questionId' => $question2->id, 'answerId' => $question2->answers->first()->id],
                ['questionId' => $question3->id, 'answerId' => $question3->answers->first()->id],
            ],
        ]);

        $this->assertTrue($products->isEmpty());
    }
}
