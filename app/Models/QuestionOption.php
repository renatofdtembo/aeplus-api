<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $table = 'question_options';

    protected $fillable = [
        'text',
        'is_correct',
        'question_id'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // 🔗 Relacionamento com questão
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // 📊 Scope para opções corretas
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }
}