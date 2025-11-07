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
            'description' => 'Бесплатный тариф с базовым функционалом',
            'price' => 0,
            'is_default' => true,
        ]);

        Plan::create([
            'name' => 'Pro',
            'description' => 'Платный тариф с расширенными возможностями',
            'price' => 9.99,
            'is_default' => false,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'description' => 'Тариф для корпоративных клиентов',
            'price' => 29.99,
            'is_default' => false,
        ]);
    }
}
