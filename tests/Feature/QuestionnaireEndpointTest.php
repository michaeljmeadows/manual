<?php

namespace Tests\Feature;

use App\Models\Question;

class QuestionnaireEndpointTest extends RealDataSeedingTest
{
    public function test_the_get_questionnaire_endpoint_works(): void
    {
        $response = $this->get('/api/questionnaire');

        $response->assertStatus(200);
        $questions = Question::with('answers')->orderBy('number')->orderBy('part')->get();
        $response->assertSeeInOrder($questions->pluck('text')->toArray());
    }
}
