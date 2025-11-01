<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            // Menus principais
            ['icone' => 'LineChart', 'parent' => 0, 'sort' => 1, 'link' => '/dashboard', 'label' => 'Dashboard'],
            ['icone' => 'Building', 'parent' => 0, 'sort' => 2, 'link' => '/instituicoes', 'label' => 'Instituições'],
            ['icone' => 'Users', 'parent' => 0, 'sort' => 3, 'link' => '/utilizadores', 'label' => 'Utilizadores'],
            ['icone' => 'BookOpen', 'parent' => 0, 'sort' => 4, 'link' => '/meus-cursos', 'label' => 'Meus Cursos'],
            ['icone' => 'Settings', 'parent' => 0, 'sort' => 5, 'link' => '/configuracoes', 'label' => 'Configurações'],
            ['icone' => 'CalendarDays', 'parent' => 0, 'sort' => 6, 'link' => '/calendario', 'label' => 'Calendário'],
        ];

        Menu::insert($menus);
    }
}
