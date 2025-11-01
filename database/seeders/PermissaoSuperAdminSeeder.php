<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissaoSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Obter a função de Super Administrador
            $funcaoSuperAdmin = DB::table('funcaos')
                ->where('nome', 'Super Administrador do Sistema')
                ->first();

            if (!$funcaoSuperAdmin) {
                $this->command->error('❌ Função "Super Administrador do Sistema" não encontrada!');
                $this->command->info('Execute primeiro o FuncaoSeeder e SuperAdminSeeder.');
                return;
            }

            // 2. Obter todos os menus do sistema
            $menus = DB::table('menus')->get();

            if ($menus->isEmpty()) {
                $this->command->error('❌ Nenhum menu encontrado!');
                $this->command->info('Execute primeiro o MenusSeeder.');
                return;
            }

            // 3. Contador para estatísticas
            $totalPermissoes = 0;

            // 4. Criar permissões totais para cada menu
            foreach ($menus as $menu) {
                // Verificar se a permissão já existe
                $permissaoExistente = DB::table('permissoes')
                    ->where('menu_id', $menu->id)
                    ->where('funcao_id', $funcaoSuperAdmin->id)
                    ->exists();

                if (!$permissaoExistente) {
                    DB::table('permissoes')->insert([
                        'canView' => true,
                        'canCreate' => true,
                        'canUpdate' => true,
                        'canDelete' => true,
                        'menu_id' => $menu->id,
                        'funcao_id' => $funcaoSuperAdmin->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $totalPermissoes++;
                }
            }

            // 5. Mensagem de sucesso
            $this->command->info("✅ {$totalPermissoes} permissões concedidas ao Super Administrador!");
            $this->command->info("👤 Função: {$funcaoSuperAdmin->nome}");
            $this->command->info("📊 Total de menus: {$menus->count()}");
            
            if ($totalPermissoes === 0) {
                $this->command->info('ℹ️  Todas as permissões já estavam cadastradas.');
            }
        });
    }
}