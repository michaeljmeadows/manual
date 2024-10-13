<?php

namespace App\Http\Controllers;

use App\Actions\EvaluateAnswersActionInterface;
use App\Http\Requests\QuestionnaireAnswersRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\QuestionResource;
use App\Repositories\QuestionRepositoryInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionnaireController extends Controller
{
    public function get(QuestionRepositoryInterface $questionRepository): JsonResource
    {
        $questions = $questionRepository->get();

        return QuestionResource::collection($questions);
    }

    public function evaluate(
        QuestionnaireAnswersRequest $request,
        EvaluateAnswersActionInterface $evaluateAnswersAction
    ): JsonResource {
        try {
            $products = $evaluateAnswersAction->evaluate($request->validated());

            return ProductResource::collection($products);
        } catch (Throwable $e) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Evaluation errors',
                'data' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}
