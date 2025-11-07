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

        // 🟢 Теперь можно безопасно создать пользователя с plan_id = 1
        User::factory()->create([
            'name' => 'Liza',
            'email' => 'liza@gmail.com',
            'password' => bcrypt('password'),
            'plan_id' => 1,
        ]);
    }
}
