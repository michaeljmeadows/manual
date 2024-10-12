<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /** @var array */
    protected const QUESTIONS = [
        [
            'number' => '1',
            'text' => 'Do you have difficulty getting or maintaining an erection?',
        ],
        [
            'number' => '2',
            'text' => 'Have you tried any of the following treatments before?',
        ],
        [
            'number' => '2a',
            'text' => 'Was the Viagra or Sildenafil product you tried before effective?',
        ],
        [
            'number' => '2b',
            'text' => 'Was the Cialis or Tadalafil product you tried before effective?',
        ],
        [
            'number' => '2c',
            'text' => 'Which is your preferred treatment?',
        ],
        [
            'number' => '3',
            'text' => 'Do you have, or have you ever had, any heart or neurological conditions?',
        ],
        [
            'number' => '4',
            'text' => 'Do any of the listed medical conditions apply to you?',
        ],
        [
            'number' => '5',
            'text' => 'Are you taking any of the following drugs?',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::QUESTIONS as $seedData) {
            Question::create([
                'number' => $seedData['number'],
                'text' => $seedData['text'],
            ]);
        }
    }
}
