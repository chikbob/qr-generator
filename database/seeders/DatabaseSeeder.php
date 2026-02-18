<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🟢 Сначала создаём планы
        $this->call(PlanSeeder::class);

        // 🟢 Создаём пользователя только если его нет
        User::firstOrCreate(
            ['email' => 'liza@gmail.com'], // проверка по уникальному email
            [
                'name' => 'Liza',
                'password' => bcrypt('password'),
                'plan_id' => 1,
            ]
        );
    }
}
