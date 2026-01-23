<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Plan::create([
            'name' => 'Free',
            'description' => 'Безкоштовний тариф з базовим функціоналом',
            'price' => 0,
            'is_default' => true,
        ]);

        /* На русском
         * Бесплатный тариф с базовым функционалом
         * Платный тариф с расширенными возможностями
         * Тариф для корпоративных клиентов
         */

        Plan::create([
            'name' => 'Pro',
            'description' => 'Платний тариф із розширеними можливостями',
            'price' => 9.99,
            'is_default' => false,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'description' => 'Тариф для корпоративних клієнтів',
            'price' => 29.99,
            'is_default' => false,
        ]);
    }
}
