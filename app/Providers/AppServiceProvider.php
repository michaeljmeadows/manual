<?php

namespace App\Providers;

use App\Actions\EvaluateAnswersAction;
use App\Actions\EvaluateAnswersActionInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\QuestionRepository;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
        $this->registerActions();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected function registerRepositories(): void
    {
        app()->bind(ProductRepositoryInterface::class, ProductRepository::class);
        app()->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
    }

    protected function registerActions(): void
    {
        app()->bind(EvaluateAnswersActionInterface::class, EvaluateAnswersAction::class);
    }
}
