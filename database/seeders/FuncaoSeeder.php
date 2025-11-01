<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin_id = DB::table('departamentos')
            ->where('nome', 'Super Administrador')
            ->value('id');

        $funcoes = [
            [
                'nome' => 'Super Administrador do Sistema',
                'descricao' => 'Acesso total ao sistema com todas as permissões',
                'departamento_id' => $super_admin_id,
                'salario_base' => 0.00,
                'nivel' => 'Sistema',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('funcaos')->insert($funcoes);
    }
}