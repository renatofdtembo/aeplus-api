<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_answers';

    protected $fillable = [
        'answer',
        'justification',
        'score',
        'feedback',
        'concluido',
        'submitted_at',
        'question_id',
        'student_id'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'concluido' => 'boolean',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // 🔗 Relacionamento com questão
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // 🔗 Relacionamento com estudante (usuário)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // 🔗 Relacionamento com quiz (através da questão)
    public function quiz()
    {
        return $this->hasOneThrough(Quiz::class, Question::class, 'id', 'id', 'question_id', 'quiz_id');
    }

    // 📊 Scope para respostas concluídas
    public function scopeConcluido($query)
    {
        return $query->where('concluido', true);
    }

    // 📊 Scope para respostas de um estudante específico
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // 📊 Método para marcar como concluído
    public function markAsCompleted()
    {
        $this->update([
            'concluido' => true,
            'submitted_at' => now()
        ]);
    }

    // 📊 Método para verificar se a resposta está correta
    public function getIsCorrectAttribute()
    {
        if ($this->question->type === Question::TYPE_MULTIPLE_CHOICE) {
            // Para múltipla escolha, verificar se a resposta coincide com as opções corretas
            $correctOptions = $this->question->correctOptions->pluck('id')->toArray();
            $studentAnswers = json_decode($this->answer, true) ?? [];
            
            return empty(array_diff($correctOptions, $studentAnswers)) && 
                   empty(array_diff($studentAnswers, $correctOptions));
        }
        
        // Para outros tipos, comparar diretamente
        return $this->answer === $this->question->correct_answer;
    }
}