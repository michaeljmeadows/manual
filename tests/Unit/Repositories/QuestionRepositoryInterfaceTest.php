<?php

namespace Tests\Unit\Repositories;

use App\Models\Answer;
use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class QuestionRepositoryInterfaceTest extends TestCase
{
    protected QuestionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(QuestionRepositoryInterface::class);
    }

    public function test_repository_returns_all_questions(): void
    {
        $targets = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->count(3)
            ->create();

        $questions = $this->repository->get();

        $this->assertEquals($targets->count(), $questions->count());
        foreach ($targets as $target) {
            $this->assertTrue($questions->contains($target));
        }
    }

    public function test_repository_returns_questions_in_correct_order(): void
    {
        $targets = Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->count(5)
            ->state(new Sequence(
                ['number' => 1, 'part' => null],
                ['number' => 1, 'part' => 'a'],
                ['number' => 2, 'part' => null],
                ['number' => 100, 'part' => null],
                ['number' => 100, 'part' => 'a'],
            ))
            ->create();

        $questions = $this->repository->get();

        $targetIds = $targets->pluck('id')->toArray();
        $questionIds = $questions->pluck('id')->toArray();
        $this->assertEquals($targetIds, $questionIds);
    }

    public function test_repository_returns_questions_with_answers_loaded(): void
    {
        Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create();

        $questions = $this->repository->get();

        $this->assertTrue($questions->first()->relationLoaded('answers'));
    }

    public function test_repository_returns_questions_answers_with_next_question_loaded(): void
    {
        Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create();

        $questions = $this->repository->get();

        $this->assertTrue($questions->first()->answers->first()->relationLoaded('nextQuestion'));
    }

    public function test_repository_returns_questions_answers_with_product_recommendations_loaded(): void
    {
        Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create();

        $questions = $this->repository->get();

        $this->assertTrue($questions->first()->answers->first()->relationLoaded('productsRecommended'));
    }

    public function test_repository_returns_questions_answers_with_product_exclusions_loaded(): void
    {
        Question::factory()
            ->has(Answer::factory()->state(['next_question_id' => null]))
            ->create();

        $questions = $this->repository->get();

        $this->assertTrue($questions->first()->answers->first()->relationLoaded('productsExcluded'));
    }
}
