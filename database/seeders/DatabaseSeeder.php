<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Outros seeders...
            DepartamentoSeeder::class,
            FuncaoSeeder::class,
            MenusSeeder::class,
            SuperAdminSeeder::class,
            PermissaoSuperAdminSeeder::class,
        ]);
    }
}
