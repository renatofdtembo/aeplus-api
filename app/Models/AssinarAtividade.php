<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssinarAtividade extends Model
{
    use HasFactory;

    protected $table = 'assinar_atividade';

    protected $fillable = [
        'id_atividade',
        'id_usuario',
    ];

    public function atividade()
    {
        return $this->belongsTo(Atividade::class, 'id_atividade');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
