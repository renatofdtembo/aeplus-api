<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'text',
        'justification',
        'type',
        'correct_answer',
        'requires_justification',
        'points',
        'order',
        'quiz_id'
    ];

    protected $casts = [
        'requires_justification' => 'boolean',
        'points' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos de questões disponíveis
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_OPEN_ENDED = 'open_ended';
    const TYPE_FILL_BLANK = 'fill_blank';

    // 🔗 Relacionamento com quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // 🔗 Relacionamento com opções
    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('id');
    }

    // 🔗 Relacionamento com respostas dos estudantes
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    // 📊 Scope para ordenação
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    // 📊 Método para verificar se é questão de múltipla escolha
    public function getIsMultipleChoiceAttribute()
    {
        return $this->type === self::TYPE_MULTIPLE_CHOICE;
    }

    // 📊 Método para obter opções corretas
    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }
}