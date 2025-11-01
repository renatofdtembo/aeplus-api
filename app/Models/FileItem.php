<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\Log;

class FileItem extends Model
{
    protected $table = 'file_items';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'type',
        'path',
        'size',
        'extension',
        'modifiedAt',
        'createdAt',
        'permissions',
        'parent_id',
        'entity_id'
    ];

    protected $casts = [
        'permissions' => 'array',
        'modifiedAt' => 'datetime',
        'createdAt' => 'datetime'
    ];

    // ➕ Incluir automaticamente o campo "nome" ao serializar o modelo
    protected $appends = ['nome'];

    // 🔗 Relacionamento com Pessoa
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entity_id');
    }

    // 🧠 Accessor para o nome da pessoa relacionada
    public function getNomeAttribute()
    {
        // Pega a pessoa relacionada
        $pessoa = $this->pessoa;

        if (!$pessoa) return null;
        
        // Se não for aluno ou faltarem dados, retorna só o nome
        return $pessoa->nome;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FileItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(FileItem::class, 'parent_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public static function getFullStructure($files)
    {
        return self::buildStructureFromCollection($files);
    }

    protected static function buildStructureFromCollection($items, $parentId = null)
    {
        return $items
            ->filter(function ($item) use ($parentId) {
                return $item->parent_id === $parentId;
            })
            ->map(function ($item) use ($items) {
                $children = self::buildStructureFromCollection($items, $item->id);

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'nome' => $item->nome, // 👈 Aqui está o nome da Pessoa
                    'type' => $item->type,
                    'path' => $item->path,
                    'size' => $item->size,
                    'extension' => $item->extension,
                    'modifiedAt' => optional($item->modifiedAt)->toISOString(),
                    'createdAt' => optional($item->createdAt)->toISOString(),
                    'permissions' => $item->permissions ?? [
                        'departments' => [],
                        'isPublic' => false
                    ],
                    'children' => $children->toArray()
                ];
            })->values();
    }

    public static function getChildrenStructure($parentId)
    {
        return self::where('parent_id', $parentId)
            ->with(['children', 'pessoa']) // 👈 Carregando relação com Pessoa
            ->withCount('children')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'nome' => $item->nome, // 👈 Inclui nome aqui também
                    'type' => $item->type,
                    'path' => $item->path,
                    'size' => $item->size,
                    'extension' => $item->extension,
                    'hasChildren' => $item->children_count > 0,
                    'permissions' => $item->permissions ?? [
                        'departments' => [],
                        'isPublic' => false
                    ]
                ];
            });
    }

    public static function seedInitialData()
    {
        if (self::count() > 0) return;

        self::create([
            'id' => 'root',
            'name' => 'Root',
            'type' => 'folder',
            'path' => '/',
            'modifiedAt' => now(),
            'createdAt' => now(),
            'permissions' => [
                'departments' => ['all'],
                'isPublic' => true
            ]
        ]);
    }
}
