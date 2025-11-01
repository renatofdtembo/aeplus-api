<?php

namespace App\Models;

use App\Enums\TipoComentario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityComent extends Model
{
    use HasFactory;

    protected $table = 'atividade_coments';

    protected $fillable = [
        'message',
        'tipo',
        'id_pai',
        'id_atividade',
        'id_usuario',
    ];

    protected $casts = [
        'tipo' => TipoComentario::class,
    ];

    public function pai()
    {
        return $this->belongsTo(self::class, 'id_pai');
    }

    public function respostas()
    {
        return $this->hasMany(self::class, 'id_pai');
    }

    public function atividade()
    {
        return $this->belongsTo(Atividade::class, 'id_atividade');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
