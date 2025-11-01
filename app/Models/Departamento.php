<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'nome',
        'categoria',
        'diretor_id',
    ];

    protected $with = ['diretor'];
    protected $hidden = ['diretor_id'];

    public function diretor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diretor_id');
    }

    public function funcoes(): HasMany
    {
        return $this->hasMany(Funcao::class, 'departamento_id');
    }

    public function permissoes(): HasMany
    {
        return $this->hasMany(Permissoes::class, 'departamento_id');
    }
}
