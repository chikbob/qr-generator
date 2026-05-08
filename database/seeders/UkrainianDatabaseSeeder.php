<?php

namespace Database\Seeders;

class UkrainianDatabaseSeeder extends LocalizedDatabaseSeeder
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
                    'description' => 'Безкоштовний тариф з базовим функціоналом',
                    'price' => 0,
                    'is_default' => true,
                ],
                [
                    'name' => 'Pro',
                    'description' => 'Платний тариф із розширеними можливостями',
                    'price' => 9.99,
                    'is_default' => false,
                ],
                [
                    'name' => 'Enterprise',
                    'description' => 'Тариф для корпоративних клієнтів',
                    'price' => 29.99,
                    'is_default' => false,
                ],
            ],

            'users' => [
                'liza_name' => 'Ліза',
                'admin_name' => 'Адмін',
                'user_name' => 'Користувач',
                'seed_user_name' => 'Тестовий користувач %02d',
            ],

            'qr' => [
                'url_hosts' => [
                    'https://example.com',
                    'https://google.com',
                    'https://maps.google.com',
                    'https://wikipedia.org',
                ],
                'email_subject' => 'Запит до підтримки #%d',
                'email_body' => 'Вітаємо, це тестовий вміст email QR-коду #%d.',
                'phone_prefix' => '+38067',
                'sms_phone_prefix' => '+38097',
                'sms_message' => 'Тестове SMS-повідомлення #%d',
                'text_content' => 'Тестовий текстовий вміст QR-коду #%d',
            ],

            'scans' => [
                'country_city_map' => [
                    'RU' => ['Москва', 'Санкт-Петербург', 'Таганрог', 'Казань'],
                    'BY' => ['Мінськ', 'Брест', 'Гомель'],
                    'UA' => ['Київ', 'Львів', 'Одеса'],
                    'PL' => ['Варшава', 'Краків'],
                    'DE' => ['Берлін', 'Мюнхен'],
                    'TR' => ['Стамбул', 'Анкара'],
                    'US' => ['Нью-Йорк', 'Чикаго'],
                    'KZ' => ['Алмати', 'Астана'],
                ],
                'weighted_countries' => ['UA', 'UA', 'UA', 'UA', 'PL', 'DE', 'TR', 'US', 'KZ', 'BY', 'RU'],
            ],

            'feedback' => [
                'subject' => 'Тестова тема звернення %02d',
                'message' => 'Це згенероване звернення %02d для перевірки роботи підтримки та адмін-панелі.',
            ],
        ];
    }
}
