<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'pai',
        'nome',
        'data_criacao',
        'data_atualizacao',
    ];

    public $timestamps = false; // Desabilita created_at e updated_at automáticos

    // ✅ Auto-preenche as datas como em @PrePersist e @PreUpdate
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($categoria) {
            $categoria->data_criacao = now();
        });

        static::updating(function ($categoria) {
            $categoria->data_atualizacao = now();
        });
    }

    // ✅ Relação opcional: uma categoria pode ter uma categoria pai
    public function categoriaPai()
    {
        return $this->belongsTo(Categoria::class, 'pai');
    }

    // ✅ Relação opcional: uma categoria pode ter várias subcategorias
    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'pai');
    }

    // Relacionamento recursivo para carregar todas as subcategorias
    public function subcategoriasRecursivas(): HasMany
    {
        return $this->hasMany(Categoria::class, 'pai')
                    ->with('subcategoriasRecursivas');
    }
}
