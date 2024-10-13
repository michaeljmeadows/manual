<?php

namespace App\Rules;

use Closure;

class QuestionExistsInCachedData extends QuestionnaireAwareRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $questions = $this->questionRepository->get();

        if ($questions->contains('id', $value)) {
            return;
        }

        $fail('The :attribute field must be a valid question ID.');
    }
}
