<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'nome',
        'nome_fantasia',
        'urlImg',
        'contacto',
        'email',
        'nif',
        'bi',
        'passaporte',
        'genero',
        'estadocivil',
        'nascimento',
        'biografia',
        'tipo',
        'endereco_id',
    ];

    protected $casts = [
        'nascimento' => 'date',
    ];

    // 🔗 Relacionamento com User (1:1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Relacionamento com Endereco
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }
}
