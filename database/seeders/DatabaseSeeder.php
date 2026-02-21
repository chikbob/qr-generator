<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        $fixedUsers = [
            [
                'email' => 'liza@gmail.com',
                'name' => 'Liza',
                'password' => bcrypt('password'),
                'plan_id' => 1,
                'is_admin' => 0,
            ],
            [
                'email' => 'admin@gmail.com',
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'plan_id' => 3,
                'is_admin' => 1,
            ],
            [
                'email' => 'user@gmail.com',
                'name' => 'User',
                'password' => bcrypt('password'),
                'plan_id' => 1,
                'is_admin' => 0,
            ],
        ];

        foreach ($fixedUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        User::factory(57)->create([
            'plan_id' => fn () => fake()->randomElement([1, 2, 3]),
            'is_admin' => false,
        ]);

        $users = User::query()->get();

        $urlHosts = [
            'https://example.com',
            'https://google.com',
            'https://maps.google.com',
            'https://wikipedia.org',
        ];

        for ($i = 0; $i < 90; $i++) {
            $type = fake()->randomElement(['text', 'url', 'email', 'tel', 'sms', 'geo']);
            $isDynamicAllowed = in_array($type, ['url', 'email', 'tel', 'sms', 'geo'], true);
            $isDynamic = $isDynamicAllowed ? fake()->boolean(60) : false;

            [$content, $payload] = $this->buildQrData($type, $urlHosts);

            QrCode::create([
                'user_id' => $users->random()->id,
                'type' => $type,
                'content' => $content,
                'payload' => $payload,
                'image_path' => 'qr_codes/seed-' . ($i + 1) . '.png',
                'size' => fake()->randomElement([180, 200, 220, 260, 300]),
                'color_dark' => fake()->randomElement(['#000000', '#1f2937', '#0f766e', '#7c2d12']),
                'color_light' => '#ffffff',
                'is_dynamic' => $isDynamic,
                'slug' => $isDynamic ? Str::lower(Str::random(10)) : null,
            ]);
        }

        $qrCodes = QrCode::query()->get();
        $countryCityMap = [
            'RU' => ['Moscow', 'Saint Petersburg', 'Taganrog', 'Kazan'],
            'BY' => ['Minsk', 'Brest', 'Gomel'],
            'UA' => ['Kyiv', 'Lviv', 'Odesa'],
            'PL' => ['Warsaw', 'Krakow'],
            'DE' => ['Berlin', 'Munich'],
            'TR' => ['Istanbul', 'Ankara'],
            'US' => ['New York', 'Chicago'],
            'KZ' => ['Almaty', 'Astana'],
        ];
        $weightedCountries = ['RU', 'RU', 'RU', 'BY', 'BY', 'UA', 'PL', 'DE', 'TR', 'US', 'KZ'];
        $browsers = ['Chrome', 'Safari', 'Firefox', 'Edge'];
        $devices = ['iPhone', 'Android', 'Macintosh', 'Windows'];

        for ($i = 0; $i < 100; $i++) {
            $country = fake()->randomElement($weightedCountries);
            $city = fake()->randomElement($countryCityMap[$country]);

            QrScan::create([
                'qr_code_id' => $qrCodes->random()->id,
                'ip' => fake()->ipv4(),
                'country' => $country,
                'city' => $city,
                'user_agent' => fake()->userAgent(),
                'device' => fake()->randomElement($devices),
                'browser' => fake()->randomElement($browsers),
                'referer' => fake()->boolean(70) ? fake()->randomElement($urlHosts) . '/history' : null,
            ]);
        }

        foreach ($qrCodes as $qrCode) {
            $qrCode->update(['scan_count' => $qrCode->scans()->count()]);
        }

        for ($i = 0; $i < 70; $i++) {
            Feedback::create([
                'user_id' => $users->random()->id,
                'subject' => fake()->sentence(4),
                'message' => fake()->paragraph(2),
                'category' => fake()->randomElement(['general', 'bug', 'idea', 'payment']),
                'status' => fake()->randomElement(['new', 'in_progress', 'resolved']),
                'priority' => fake()->randomElement(['low', 'medium', 'high']),
            ]);
        }
    }

    private function buildQrData(string $type, array $urlHosts): array
    {
        return match ($type) {
            'url' => (function () use ($urlHosts) {
                $url = fake()->randomElement($urlHosts) . '/' . fake()->slug();
                return [$url, ['url' => $url]];
            })(),
            'email' => (function () {
                $email = fake()->safeEmail();
                $subject = fake()->sentence(3);
                $body = fake()->sentence(8);
                return ["mailto:{$email}?subject=" . rawurlencode($subject) . '&body=' . rawurlencode($body), [
                    'email' => $email,
                    'subject' => $subject,
                    'body' => $body,
                ]];
            })(),
            'tel' => (function () {
                $phone = '+7' . fake()->numerify('9#########');
                return ["tel:{$phone}", ['phone' => $phone]];
            })(),
            'sms' => (function () {
                $phone = '+7' . fake()->numerify('9#########');
                $message = fake()->sentence(6);
                return ["sms:{$phone}?body=" . rawurlencode($message), [
                    'phone' => $phone,
                    'message' => $message,
                ]];
            })(),
            'geo' => (function () {
                $lat = fake()->latitude(40, 60);
                $lng = fake()->longitude(20, 50);
                return ["geo:{$lat},{$lng}", ['lat' => $lat, 'lng' => $lng]];
            })(),
            default => (function () {
                $text = fake()->sentence(10);
                return [$text, ['text' => $text]];
            })(),
        };
    }
}
