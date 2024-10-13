<?php

namespace App\Http\Requests;

use App\Repositories\QuestionRepositoryInterface;
use App\Rules\AnswerBelongsToQuestion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class QuestionnaireAnswersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(QuestionRepositoryInterface $questionRepository): array
    {
        $answerBelongsToQuestion = new AnswerBelongsToQuestion($questionRepository);

        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'integer', 'exists:questions,id'],
            'answers.*.answerId' => ['required', 'integer', 'exists:answers,id', $answerBelongsToQuestion],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
