<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /** @var string[] */
    protected $fillable = [
        'reference',
        'name',
        'dosage_mg',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'dosage' => 'integer',
        ];
    }
}
