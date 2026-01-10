<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'question',
        'subtext',
        'type',
        'options',
        'has_other',
        'order',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'has_other' => 'boolean',
        'is_active' => 'boolean',
    ];
}
