<?php

namespace Tests\Unit\Rules;

use App\Repositories\QuestionRepositoryInterface;
use Tests\TestCase;

abstract class QuestionAwareRuleTest extends TestCase
{
    protected QuestionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(QuestionRepositoryInterface::class);
    }
}
