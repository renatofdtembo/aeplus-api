<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'label',
        'link',
        'icone',
        'parent',
        'sort',
    ];

    /**
     * Relação para menus filhos diretos
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent')->orderBy('sort', 'asc');
    }

    /**
     * Relação recursiva para carregar todos os níveis de submenus
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Relação para o menu pai
     */
    public function parentMenu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent');
    }
}
