<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permissoes extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    protected $fillable = [
        'canView',
        'canCreate',
        'canUpdate',
        'canDelete',
        'menu_id',
        'funcao_id',
    ];

    protected $casts = [
        'canView' => 'boolean',
        'canCreate' => 'boolean',
        'canUpdate' => 'boolean',
        'canDelete' => 'boolean',
    ];

    protected $with = ['menu', 'funcao'];
    protected $hidden = ['funcao_id', 'menu_id'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function funcao(): BelongsTo
    {
        return $this->belongsTo(Funcao::class, 'funcao_id');
    }
}
