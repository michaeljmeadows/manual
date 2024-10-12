<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireController extends Controller
{
    public function get(QuestionRepositoryInterface $questionRepository): JsonResource
    {
        $questions = $questionRepository->get();

        return QuestionResource::collection($questions);
    }
}
