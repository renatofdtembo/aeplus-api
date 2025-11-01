<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\{User, UserDetail};
use App\Services\OperationLogger;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Iniciando criação do Super Admin...');

        DB::beginTransaction();

        try {
            // 1️⃣ Criar usuário base
            $user = User::firstOrCreate(
                ['email' => 'admin@aeplus.ao'],
                [
                    'name' => 'superadmin',
                    'password' => Hash::make('admin123'),
                    'status' => 'online',
                    'update_password' => false,
                    'ultimo_acesso' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // 2️⃣ Criar detalhes do usuário
            $userDetail = UserDetail::firstOrCreate(
                ['email' => 'admin@sigumussulo.ao'],
                [
                    'nome' => 'Administrador do Sistema',
                    'genero' => 'M',
                    'tipo' => 'PESSOA',
                    'nif' => '1000000000',
                    'bi' => '000000000LA000',
                    'contacto' => '+244900000000',
                    'nascimento' => '1990-01-01',
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // 5️⃣ Obter função “Super Administrador do Sistema”
            $funcaoSuperAdmin = DB::table('funcaos')
                ->where('nome', 'Super Administrador do Sistema')
                ->first();

            if ($funcaoSuperAdmin) {
                $exists = DB::table('user_funcao')
                    ->where('user_id', $user->id)
                    ->where('funcao_id', $funcaoSuperAdmin->id)
                    ->exists();

                if (!$exists) {
                    DB::table('user_funcao')->insert([
                        'user_id' => $user->id,
                        'funcao_id' => $funcaoSuperAdmin->id,
                        'data_inicio' => now(),
                        'data_cadastro' => now(),
                        'data_atualizacao' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // 6️⃣ Registrar operação e mensagens no console
            OperationLogger::log('create', $user, 'Super Admin criado com sucesso!', null, request());

            DB::commit();

            $this->command->info('✅ Super Admin criado com sucesso!');
            $this->command->info('📧 Email: admin@sigumussulo.ao');
            $this->command->info('🔑 Senha: admin123');
            $this->command->info('👤 Nome: ' . $userDetail->nome);
            $this->command->info('💼 Função: Super Administrador do Sistema');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('❌ Erro ao criar o Super Admin: ' . $e->getMessage());
        }
    }
}
