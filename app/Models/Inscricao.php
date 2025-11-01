<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    use HasFactory;

    protected $table = 'inscricao';

    protected $fillable = [
        'configuracoes',
        'status',
        'nota',
        'curso_id',
        'user_id',
    ];

    protected $casts = [
        'nota' => 'double',
        'status' => \App\Enums\StatusInscricao::class,
    ];

    /**
     * Relação com Curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Relação com Usuário (Estudante)
     */
    public function estudante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
