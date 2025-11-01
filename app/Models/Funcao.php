<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    use HasFactory;

    protected $table = 'funcaos';

    protected $fillable = [
        'nome',
        'descricao',
        'departamento_id',
        'salario_base',
        'nivel',
        'ativo',
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    protected $with = ['departamento'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_funcao')
            ->withTimestamps()
            ->withPivot(['data_inicio', 'data_fim']);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function permissoes()
    {
        return $this->hasMany(Permissoes::class);
    }
}
