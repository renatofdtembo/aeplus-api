<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $fillable = [
        'nome',
        'ordem',
        'peso',
        'configuracoes',
        'curso_id',
    ];

    protected $casts = [
        'peso' => 'double',
        'configuracoes' => 'array', // Converte JSON automaticamente
    ];

    /**
     * Relação com Curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
