<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemEvaluation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'functional_suitability' => 'float',
            'performance_efficiency' => 'float',
            'compatibility' => 'float',
            'usability' => 'float',
            'reliability' => 'float',
            'security' => 'float',
            'maintainability' => 'float',
            'portability' => 'float',
            'overall_mean' => 'float',
            'detailed_answers' => 'array',
            'submitted_at' => 'datetime',
        ];
    }
}

