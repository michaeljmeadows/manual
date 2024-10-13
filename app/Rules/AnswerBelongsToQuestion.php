<?php

namespace App\Rules;

use App\Repositories\QuestionRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class AnswerBelongsToQuestion implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(protected QuestionRepositoryInterface $questionRepository) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $answerId = $value;
        $index = $this->getArrayIndex($attribute);
        
        $questionAttribute = str_replace('answer', 'question', $attribute);

        if (! isset($this->data['answers'][$index]['questionId'])) {
            $fail('The :attribute field must be paired with a '.$questionAttribute.' field.');

            return;
        }

        $questionId = $this->data['answers'][$index]['questionId'];

        $questions = $this->questionRepository->get()->keyBy('id');

        if ($questions[$questionId]->answers->contains('id', $answerId)) {
            return;
        }

        $fail('The :attribute field must belong to the '.$questionAttribute.' field.');
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    protected function getArrayIndex(string $attribute): ?int
    {
        preg_match('/answers\.(\d+)/', $attribute, $matches);

        return isset($matches[1])
            ? (int) $matches[1]
            : null;
    }
}
