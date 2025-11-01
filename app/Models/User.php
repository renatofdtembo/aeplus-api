<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_code',
        'name',
        'email',
        'status',
        'update_password',
        'ultimo_acesso',
        'password',
        'biografia', 
        'update_password'
    ];
 
    protected $with = ['detail'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'update_password' => 'boolean'
    ];
 
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->user_code)) {
                $user->user_code = static::generateUniqueUserCode();
            }
        });
    }

    /**
     * Generate a unique user code
     */
    protected static function generateUniqueUserCode(): string
    {
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            // Gera um código no formato: UC + 8 caracteres alfanuméricos (ex: UC1A2B3C4D)
            $code = 'UC' . Str::upper(Str::random(8));
            
            // Verifica se o código já existe
            $exists = static::where('user_code', $code)->exists();
            
            $attempt++;
            
            if (!$exists) {
                return $code;
            }
            
        } while ($attempt < $maxAttempts);
        
        // Se após 10 tentativas ainda não encontrou um único, usa timestamp
        return 'UC' . Str::upper(Str::random(6)) . time();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // 🔗 Relacionamento com UserDetails
    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    // 🔗 Relacionamento com Funcao
    public function funcoes()
    {
        return $this->belongsToMany(Funcao::class, 'user_funcao')
            ->withTimestamps()
            ->withPivot(['data_inicio', 'data_fim']);
    }
}
