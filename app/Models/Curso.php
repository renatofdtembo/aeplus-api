<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'capa',
        'url_image',
        'titulo',
        'nome_breve',
        'descricao',
        'preco',
        'gratuito',
        'inscricao',
        'data_inicio_inscricao',
        'data_fim_inscricao',
        'data_inicio',
        'data_termino',
        'categoria_id',
        'responsavel_id',
        'instituicao_id',
        'duracao',
        'nivel',
        'privacidade',
        'oqueaprender',
        'sobre',
        'video_introducao',
        'tipo',
        'visibilidade',
        'configuracoes',
        'data_criacao',
        'data_atualizacao',
    ];

    public $timestamps = false;

    // ✅ Preenchimento automático das datas
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($curso) {
            $curso->data_criacao = now();
        });

        static::updating(function ($curso) {
            $curso->data_atualizacao = now();
        });
    }

    // ✅ Relacionamentos
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function instituicao()
    {
        return $this->belongsTo(User::class, 'instituicao_id');
    }

    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'curso_id')->orderBy('ordem');
    }

    // ✅ Constantes ENUM (substituem os Enums Java)
    public const DURACOES = ['UM_MES', 'TRES_MESES', 'SEIS_MESES', 'UM_ANO'];
    public const NIVEIS = ['INICIANTE', 'INTERMEDIARIO', 'AVANCADO', 'CURSO_PROFISSIONAL'];
    public const PRIVACIDADES = ['PUBLICO', 'PRIVADO'];
}
