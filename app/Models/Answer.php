<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Answer extends Model
{
    /** @use HasFactory<\Database\Factories\AnswerFactory> */
    use HasFactory;

    /** @var string[] */
    protected $fillable = [
        'question_id',
        'next_question_id',
        'order',
        'text',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function nextQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'next_question_id');
    }

    public function productsExcluded(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'answer_excludes');
    }

    public function productsRecommended(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'answer_recommends');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
