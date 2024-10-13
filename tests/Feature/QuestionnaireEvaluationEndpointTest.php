<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;

class QuestionnaireEvaluationEndpointTest extends RealDataSeedingTest
{
    protected Collection $questions;

    public function setUp(): void
    {
        parent::setUp();

        $this->questions = Question::with('answers')->get()->keyBy('number_and_part');
    }

    public static function answersProvider(): array
    {
        return [
            'never had issues' => [
                [
                    '1' => 'No',
                    '2' => 'None of the above',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                [],
            ],
            'never tried any medication' => [
                [
                    '1' => 'Yes',
                    '2' => 'None of the above',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                ['sildenafil_50', 'tadalafil_10'],
            ],
            'has tried viagra and was effective' => [
                [
                    '1' => 'Yes',
                    '2' => 'Viagra or Sildenafil',
                    '2a' => 'Yes',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                ['sildenafil_50'],
            ],
            'has tried viagra and was not effective' => [
                [
                    '1' => 'Yes',
                    '2' => 'Viagra or Sildenafil',
                    '2a' => 'No',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                ['tadalafil_20'],
            ],
            'has tried cialis and was effective' => [
                [
                    '1' => 'Yes',
                    '2' => 'Cialis or Tadalafil',
                    '2b' => 'Yes',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                ['tadalafil_10'],
            ],
            'has tried cialis and was not effective' => [
                [
                    '1' => 'Yes',
                    '2' => 'Cialis or Tadalafil',
                    '2b' => 'No',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                ['sildenafil_100'],
            ],
            'has tried viagra but has heart condition' => [
                [
                    '1' => 'Yes',
                    '2' => 'Viagra or Sildenafil',
                    '2a' => 'Yes',
                    '3' => 'Yes',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'I don\'t take any of these drugs',
                ],
                [],
            ],
            'has tried cialis but has medical condition' => [
                [
                    '1' => 'Yes',
                    '2' => 'Cialis or Tadalafil',
                    '2b' => 'Yes',
                    '3' => 'No',
                    '4' => 'Significant liver problems (such as cirrhosis of the liver) or kidney problems',
                    '5' => 'I don\'t take any of these drugs',
                ],
                [],
            ],
            'takes tried viagra but on medication' => [
                [
                    '1' => 'Yes',
                    '2' => 'Viagra or Sildenafil',
                    '2a' => 'Yes',
                    '3' => 'No',
                    '4' => 'I don\'t have any of these conditions',
                    '5' => 'Cimetidine (for heartburn)',
                ],
                [],
            ],
        ];
    }

    #[DataProvider('answersProvider')]
    public function test_evaluation_endpoint_recommends_correct_products(array $answers, array $references): void
    {
        $json = $this->parseAnswers($answers);

        $response = $this->postJson('/api/questionnaire/answers', ['answers' => $json]);

        $response->assertStatus(200);

        $this->assertEqualsCanonicalizing($references, array_column($response->json()['data'], 'reference'));
    }

    public function test_invalid_request_fails_gracefully(): void
    {
        $response = $this->postJson('/api/questionnaire/answers', ['answers' => [1, 999999]]);

        $response->assertStatus(422);
        $this->assertEquals('Validation errors', $response->json()['message']);
    }

    public function test_incomplete_questionniare_fails_gracefully(): void
    {
        $json = $this->parseAnswers([
            '1' => 'No',
        ]);

        $response = $this->postJson('/api/questionnaire/answers', ['answers' => $json]);

        $response->assertStatus(422);
        $this->assertEquals('Evaluation errors', $response->json()['message']);
    }

    protected function parseAnswers(array $answers): array
    {
        $data = [];

        foreach ($answers as $questionNumberAndPart => $answer) {
            $data[] = [
                'questionId' => $this->questions[$questionNumberAndPart]->id,
                'answerId' => $this->questions[$questionNumberAndPart]->answers->firstWhere('text', $answer)->id,
            ];
        }

        return $data;
    }
}
