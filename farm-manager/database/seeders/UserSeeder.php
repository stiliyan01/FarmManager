<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    // Паролата на всички демо потребители е "password".
    public function run(): void
    {
        $shumen = Region::where('name', 'Шумен')->first();

        User::factory()->admin()->create(['name' => 'Администратор', 'email' => 'admin@stado.test']);

        // ОДБХ Шумен
        User::factory()->registrar($shumen)->create(['name' => 'Петя Николова', 'email' => 'registrar@stado.test']);
        User::factory()->inspector($shumen)->create(['name' => 'Георги Стоянов', 'email' => 'inspector@stado.test']);

        // Ветеринарни лекари
        User::factory()->vet()->create(['name' => 'д-р Иван Петров', 'email' => 'vet@stado.test']);
        User::factory()->vet()->create(['name' => 'д-р Мария Димитрова', 'email' => 'vet2@stado.test']);
        User::factory()->vet()->create(['name' => 'д-р Калин Тодоров', 'email' => 'vet3@stado.test']); // без назначени ферми

        // Собственици
        User::factory()->create(['name' => 'Стоян Иванов', 'email' => 'owner@stado.test']);
        User::factory()->create(['name' => 'Елена Георгиева', 'email' => 'owner2@stado.test']);
        User::factory()->create(['name' => 'Николай Василев', 'email' => 'owner3@stado.test']);

        // Собствениците на по-малките ферми в областта
        User::factory()->create(['name' => 'Димитър Колев', 'email' => 'owner4@stado.test']);
        User::factory()->create(['name' => 'Мария Петкова', 'email' => 'owner5@stado.test']);
        User::factory()->create(['name' => 'Георги Атанасов', 'email' => 'owner6@stado.test']);
        User::factory()->create(['name' => 'Иванка Тодорова', 'email' => 'owner7@stado.test']);
        User::factory()->create(['name' => 'Петър Стефанов', 'email' => 'owner8@stado.test']);
        User::factory()->create(['name' => 'Росица Илиева', 'email' => 'owner9@stado.test']);
    }
}
