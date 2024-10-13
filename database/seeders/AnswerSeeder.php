<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Product;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AnswerSeeder extends Seeder
{
    /** @var array */
    protected const ANSWERS = [
        '1' => [
            [
                'text' => 'Yes',
                'nextQuestionNumber' => 2,
            ],
            [
                'text' => 'No',
                'nextQuestionNumber' => 2,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
        ],
        '2' => [
            [
                'text' => 'Viagra or Sildenafil',
                'nextQuestionNumber' => 2,
                'nextQuestionPart' => 'a',
            ],
            [
                'text' => 'Cialis or Tadalafil',
                'nextQuestionNumber' => 2,
                'nextQuestionPart' => 'b',
            ],
            [
                'text' => 'Both',
                'nextQuestionNumber' => 2,
                'nextQuestionPart' => 'c',
            ],
            [
                'text' => 'None of the above',
                'nextQuestionNumber' => 3,
                'recommendations' => ['sildenafil_50', 'tadalafil_10'],
            ],
        ],
        '2a' => [
            [
                'text' => 'Yes',
                'nextQuestionNumber' => 3,
                'recommendations' => ['sildenafil_50'],
                'exclusions' => ['tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'No',
                'nextQuestionNumber' => 3,
                'recommendations' => ['tadalafil_20'],
                'exclusions' => ['sildenafil_50', 'sildenafil_100'],
            ],
        ],
        '2b' => [
            [
                'text' => 'Yes',
                'nextQuestionNumber' => 3,
                'recommendations' => ['tadalafil_10'],
                'exclusions' => ['sildenafil_50', 'sildenafil_100'],
            ],
            [
                'text' => 'No',
                'nextQuestionNumber' => 3,
                'recommendations' => ['sildenafil_100'],
                'exclusions' => ['tadalafil_10', 'tadalafil_20'],
            ],
        ],
        '2c' => [
            [
                'text' => 'Viagra or Sildenafil',
                'nextQuestionNumber' => 3,
                'recommendations' => ['sildenafil_100'],
                'exclusions' => ['tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Cialis or Tadalafil',
                'nextQuestionNumber' => 3,
                'recommendations' => ['tadalafil_20'],
                'exclusions' => ['sildenafil_50', 'sildenafil_100'],
            ],
            [
                'text' => 'None of the above',
                'nextQuestionNumber' => 3,
                'recommendations' => ['sildenafil_100', 'tadalafil_20'],
            ],
        ],
        '3' => [
            [
                'text' => 'Yes',
                'nextQuestionNumber' => 4,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'No',
                'nextQuestionNumber' => 4,
            ],
        ],
        '4' => [
            [
                'text' => 'Significant liver problems (such as cirrhosis of the liver) or kidney problems',
                'nextQuestionNumber' => 5,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Currently prescribed GTN, Isosorbide mononitrate, Isosorbide dinitrate, Nicorandil (nitrates) or Rectogesic ointment',
                'nextQuestionNumber' => 5,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Abnormal blood pressure (lower than 90/50 mmHg or higher than 160/90 mmHg)',
                'nextQuestionNumber' => 5,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Condition affecting your penis (such as Peyronie\'s Disease, previous injuries or an inability to retract your foreskin)',
                'nextQuestionNumber' => 5,
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'I don\'t have any of these conditions',
                'nextQuestionNumber' => 5,
            ],
        ],
        '5' => [
            [
                'text' => 'Alpha-blocker medication such as Alfuzosin, Doxazosin, Tamsulosin, Prazosin, Terazosin or over-the-counter Flomax',
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Riociguat or other guanylate cyclase stimulators (for lung problems)',
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Saquinavir, Ritonavir or Indinavir (for HIV)',
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'Cimetidine (for heartburn)',
                'exclusions' => ['sildenafil_50', 'sildenafil_100', 'tadalafil_10', 'tadalafil_20'],
            ],
            [
                'text' => 'I don\'t take any of these drugs',
            ],
        ],
    ];

    protected Collection $products;

    protected Collection $questions;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->products = Product::all()->keyBy('reference');
        $this->questions = Question::all()->keyBy('number_and_part');

        foreach (self::ANSWERS as $questionNumber => $answers) {
            $question = $this->questions[$questionNumber];
            $this->seedAnswersForQuestion($question, $answers);
        }
    }

    protected function seedAnswersForQuestion(Question $question, array $answers): void
    {
        foreach ($answers as $index => $answerData) {
            $nextQuestion = $this->getNextQuestion($answerData);

            $answer = Answer::create([
                'question_id' => $question->id,
                'next_question_id' => $nextQuestion?->id,
                'order' => $index + 1,
                'text' => $answerData['text'],
            ]);

            $this->seedAnswerRecommendations($answer, $answerData);
            $this->seedAnswerExclusions($answer, $answerData);
        }
    }

    protected function getNextQuestion(array $answerData): ?Question
    {
        if (! isset($answerData['nextQuestionNumber'])) {
            return null;
        }

        $nextQuestionNumber = $answerData['nextQuestionNumber'];
        $nextQuestionPart = $answerData['nextQuestionPart'] ?? '';
        $key = $nextQuestionNumber.$nextQuestionPart;

        return $this->questions[$key] ?? null;
    }

    protected function seedAnswerRecommendations(Answer $answer, array $answerData): void
    {
        if (! isset($answerData['recommendations'])) {
            return;
        }

        $recommendations = [];

        foreach ($answerData['recommendations'] as $recommendation) {
            $recommendations[] = $this->products[$recommendation]->id;
        }

        $answer->productsRecommended()->attach($recommendations);
    }

    protected function seedAnswerExclusions(Answer $answer, array $answerData): void
    {
        if (! isset($answerData['exclusions'])) {
            return;
        }

        $exclusions = [];

        foreach ($answerData['exclusions'] as $exclusion) {
            $exclusions[] = $this->products[$exclusion]->id;
        }

        $answer->productsExcluded()->attach($exclusions);
    }
}
