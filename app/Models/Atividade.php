<?php

namespace App\Models;

use App\Enums\TipoAtividade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $table = 'atividades';

    protected $fillable = [
        'titulo',
        'conteudo',
        'configuracoes',
        'tipo',
        'status',
        'change_aba',
        'required_camera',
        'posicao',
        'peso',
        'id_modulo',
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'status' => 'boolean',
        'change_aba' => 'boolean',
        'required_camera' => 'boolean',
        'tipo' => TipoAtividade::class,
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }

    public function comentarios()
    {
        return $this->hasMany(ActivityComent::class, 'id_atividade');
    }

    public function assinaturas()
    {
        return $this->hasMany(AssinarAtividade::class, 'id_atividade');
    }
}
