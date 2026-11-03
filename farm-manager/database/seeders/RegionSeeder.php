<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Благоевград', 'Бургас', 'Варна', 'Велико Търново', 'Видин', 'Враца', 'Габрово',
            'Добрич', 'Кърджали', 'Кюстендил', 'Ловеч', 'Монтана', 'Пазарджик', 'Перник',
            'Плевен', 'Пловдив', 'Разград', 'Русе', 'Силистра', 'Сливен', 'Смолян',
            'София-град', 'София', 'Стара Загора', 'Търговище', 'Хасково', 'Шумен', 'Ямбол',
        ];

        foreach ($regions as $name) {
            Region::create(['name' => $name]);
        }
    }
}
