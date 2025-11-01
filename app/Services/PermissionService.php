<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\{Funcionario, User};

class PermissionService
{
    public function getUserPermissions(User $user): UserPermissions
    {
        return Cache::remember("user.{$user->id}.permissions", now()->addDay(), function() use ($user) {
            $permissoes = $user->funcoes()
                ->where(function($query) {
                    $query->whereNull('funcionario_funcao.data_fim')
                          ->orWhere('funcionario_funcao.data_fim', '>', now());
                })
                ->with(['permissoes.menu'])
                ->get()
                ->flatMap->permissoes;

            return new UserPermissions($this->buildPermissions($permissoes));
        });
    }

    protected function buildPermissions($permissoes): array
    {
        $permissions = [];

        foreach ($permissoes as $permissao) {
            $link = $this->normalizeLink($permissao->menu->link);

            if (!isset($permissions[$link])) {
                $permissions[$link] = [
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false
                ];
            }

            $permissions[$link]['view'] = $permissions[$link]['view'] || $permissao->canView;
            $permissions[$link]['create'] = $permissions[$link]['create'] || $permissao->canCreate;
            $permissions[$link]['update'] = $permissions[$link]['update'] || $permissao->canUpdate;
            $permissions[$link]['delete'] = $permissions[$link]['delete'] || $permissao->canDelete;
        }

        return $permissions;
    }

    protected function normalizeLink(string $link): string
    {
        return '/' . ltrim($link, '/');
    }

    public function clearCache(User $user)
    {
        Cache::forget("user.{$user->id}.permissions");
    }
}

class UserPermissions
{
    /** @var array<string, array{view: bool, create: bool, update: bool, delete: bool}> */
    private array $permissions;

    /**
     * @param array<string, array{view: bool, create: bool, update: bool, delete: bool}> $permissions
     */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function canView(string $path): bool
    {
        return $this->permissions[$path]['view'] ?? false;
    }

    public function canCreate(string $path): bool
    {
        return $this->permissions[$path]['create'] ?? false;
    }

    public function canUpdate(string $path): bool
    {
        return $this->permissions[$path]['update'] ?? false;
    }

    public function canDelete(string $path): bool
    {
        return $this->permissions[$path]['delete'] ?? false;
    }

    /**
     * @return array<string, array{view: bool, create: bool, update: bool, delete: bool}>
     */
    public function all(): array
    {
        return $this->permissions;
    }
}