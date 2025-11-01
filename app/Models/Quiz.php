<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quizzes';

    protected $fillable = [
        'title',
        'description',
        'instructions',
        'time_limit',
        'is_active',
        'show_justification',
        'atividade_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_justification' => 'boolean',
        'time_limit' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // 🔗 Relacionamento com atividades
    public function atividade()
    {
        return $this->belongsTo(Atividade::class);
    }

    // 🔗 Relacionamento com questões
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // 🔗 Relacionamento com respostas dos estudantes (através de questions)
    public function studentAnswers()
    {
        return $this->hasManyThrough(StudentAnswer::class, Question::class);
    }

    // 📊 Scope para quizzes ativos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 📊 Método para calcular o total de pontos do quiz
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }
}