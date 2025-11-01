<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            // Departamento Super Admin (novo)
            [
                'nome' => 'Super Administrador',
                'categoria' => 'Sistema',
                'diretor_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Departamento::insert($departamentos);
    }
}