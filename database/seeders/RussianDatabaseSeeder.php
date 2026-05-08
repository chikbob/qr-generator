<?php

namespace Database\Seeders;

class RussianDatabaseSeeder extends LocalizedDatabaseSeeder
{
    /**
     * @return array<string, mixed>
     */
    protected function seedContent(): array
    {
        return [
            'plans' => [
                [
                    'name' => 'Free',
                    'description' => 'Бесплатный тариф с базовым функционалом',
                    'price' => 0,
                    'is_default' => true,
                ],
                [
                    'name' => 'Pro',
                    'description' => 'Платный тариф с расширенными возможностями',
                    'price' => 9.99,
                    'is_default' => false,
                ],
                [
                    'name' => 'Enterprise',
                    'description' => 'Тариф для корпоративных клиентов',
                    'price' => 29.99,
                    'is_default' => false,
                ],
            ],

            'users' => [
                'liza_name' => 'Лиза',
                'admin_name' => 'Администратор',
                'user_name' => 'Пользователь',
                'seed_user_name' => 'Тестовый пользователь %02d',
            ],

            'qr' => [
                'url_hosts' => [
                    'https://example.com',
                    'https://google.com',
                    'https://maps.google.com',
                    'https://wikipedia.org',
                ],
                'email_subject' => 'Запрос в поддержку #%d',
                'email_body' => 'Здравствуйте, это тестовое содержимое email QR-кода #%d.',
                'phone_prefix' => '+7900',
                'sms_phone_prefix' => '+7901',
                'sms_message' => 'Тестовое SMS-сообщение #%d',
                'text_content' => 'Тестовое текстовое содержимое QR-кода #%d',
            ],

            'scans' => [
                'country_city_map' => [
                    'RU' => ['Москва', 'Санкт-Петербург', 'Таганрог', 'Казань'],
                    'BY' => ['Минск', 'Брест', 'Гомель'],
                    'UA' => ['Киев', 'Львов', 'Одесса'],
                    'PL' => ['Варшава', 'Краков'],
                    'DE' => ['Берлин', 'Мюнхен'],
                    'TR' => ['Стамбул', 'Анкара'],
                    'US' => ['Нью-Йорк', 'Чикаго'],
                    'KZ' => ['Алматы', 'Астана'],
                ],
                'weighted_countries' => ['RU', 'RU', 'RU', 'BY', 'BY', 'UA', 'PL', 'DE', 'TR', 'US', 'KZ'],
            ],

            'feedback' => [
                'subject' => 'Тестовая тема обращения %02d',
                'message' => 'Это сгенерированное обращение %02d для проверки работы поддержки и админ-панели.',
            ],
        ];
    }
}
