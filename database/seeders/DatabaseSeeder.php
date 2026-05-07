<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        $planIds = Plan::query()->pluck('id', 'name');

        $this->seedUsers($planIds->all());

        $users = User::query()->orderBy('id')->get();

        $this->seedQrCodes($users);

        $qrCodes = QrCode::query()->orderBy('id')->get();

        $this->seedQrScans($qrCodes);
        $this->refreshScanCounts($qrCodes);
        $this->seedFeedback($users);
    }

    /**
     * @param array<string, int> $planIds
     */
    private function seedUsers(array $planIds): void
    {
        $fixedUsers = [
            [
                'email' => 'liza@gmail.com',
                'name' => 'Liza',
                'password' => bcrypt('password'),
                'plan_id' => $planIds['Free'],
                'is_admin' => false,
            ],
            [
                'email' => 'admin@gmail.com',
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'plan_id' => $planIds['Enterprise'],
                'is_admin' => true,
            ],
            [
                'email' => 'user@gmail.com',
                'name' => 'User',
                'password' => bcrypt('password'),
                'plan_id' => $planIds['Free'],
                'is_admin' => false,
            ],
        ];

        foreach ($fixedUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $planRotation = [
            $planIds['Free'],
            $planIds['Pro'],
            $planIds['Enterprise'],
        ];

        for ($i = 1; $i <= 57; $i++) {
            User::updateOrCreate(
                ['email' => sprintf('seed-user-%02d@example.com', $i)],
                [
                    'name' => sprintf('Seed User %02d', $i),
                    'password' => bcrypt('password'),
                    'plan_id' => $planRotation[($i - 1) % count($planRotation)],
                    'is_admin' => false,
                ]
            );
        }
    }

    private function seedQrCodes($users): void
    {
        $types = ['text', 'url', 'email', 'tel', 'sms', 'geo'];
        $urlHosts = [
            'https://example.com',
            'https://google.com',
            'https://maps.google.com',
            'https://wikipedia.org',
        ];
        $sizes = [180, 200, 220, 260, 300];
        $darkColors = ['#000000', '#1f2937', '#0f766e', '#7c2d12'];

        for ($i = 0; $i < 90; $i++) {
            $type = $types[$i % count($types)];
            $isDynamicAllowed = in_array($type, ['url', 'email', 'tel', 'sms', 'geo'], true);
            $isDynamic = $isDynamicAllowed && $i % 3 !== 0;

            [$content, $payload] = $this->buildQrData($type, $urlHosts, $i + 1);

            QrCode::create([
                'user_id' => $users[$i % $users->count()]->id,
                'type' => $type,
                'content' => $content,
                'payload' => $payload,
                'image_path' => 'qr_codes/seed-' . ($i + 1) . '.png',
                'size' => $sizes[$i % count($sizes)],
                'color_dark' => $darkColors[$i % count($darkColors)],
                'color_light' => '#ffffff',
                'is_dynamic' => $isDynamic,
                'slug' => $isDynamic ? sprintf('seed-qr-%03d', $i + 1) : null,
            ]);
        }
    }

    private function seedQrScans($qrCodes): void
    {
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
        $referers = [
            'https://example.com/history',
            'https://google.com/history',
            'https://maps.google.com/history',
            'https://wikipedia.org/history',
            null,
        ];

        for ($i = 0; $i < 100; $i++) {
            $country = $weightedCountries[$i % count($weightedCountries)];
            $cities = $countryCityMap[$country];
            $city = $cities[$i % count($cities)];

            QrScan::create([
                'qr_code_id' => $qrCodes[$i % $qrCodes->count()]->id,
                'ip' => sprintf('10.%d.%d.%d', ($i % 250) + 1, (($i * 3) % 250) + 1, (($i * 7) % 250) + 1),
                'country' => $country,
                'city' => $city,
                'user_agent' => sprintf('SeedAgent/%d.0', ($i % 5) + 1),
                'device' => $devices[$i % count($devices)],
                'browser' => $browsers[$i % count($browsers)],
                'referer' => $referers[$i % count($referers)],
            ]);
        }
    }

    private function refreshScanCounts($qrCodes): void
    {
        foreach ($qrCodes as $qrCode) {
            $qrCode->update(['scan_count' => $qrCode->scans()->count()]);
        }
    }

    private function seedFeedback($users): void
    {
        $categories = ['general', 'bug', 'idea', 'payment'];
        $statuses = ['new', 'in_progress', 'resolved'];
        $priorities = ['low', 'medium', 'high'];

        for ($i = 0; $i < 70; $i++) {
            Feedback::create([
                'user_id' => $users[$i % $users->count()]->id,
                'subject' => sprintf('Seed feedback subject %02d', $i + 1),
                'message' => sprintf(
                    'This is generated feedback message %02d for testing the support workflow and admin views.',
                    $i + 1
                ),
                'category' => $categories[$i % count($categories)],
                'status' => $statuses[$i % count($statuses)],
                'priority' => $priorities[$i % count($priorities)],
            ]);
        }
    }

    /**
     * @param array<int, string> $urlHosts
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildQrData(string $type, array $urlHosts, int $index): array
    {
        return match ($type) {
            'url' => (function () use ($urlHosts, $index) {
                $url = rtrim($urlHosts[($index - 1) % count($urlHosts)], '/') . '/seed-page-' . $index;

                return [$url, ['url' => $url]];
            })(),
            'email' => (function () use ($index) {
                $email = sprintf('contact%02d@example.com', $index);
                $subject = 'Support Request #' . $index;
                $body = 'Hello, this is seeded email QR content #' . $index . '.';
                $mailto = 'mailto:' . $email . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body);

                return [$mailto, [
                    'email' => $email,
                    'subject' => $subject,
                    'body' => $body,
                ]];
            })(),
            'tel' => (function () use ($index) {
                $phone = '+7900' . str_pad((string) $index, 7, '0', STR_PAD_LEFT);

                return ['tel:' . $phone, ['phone' => $phone]];
            })(),
            'sms' => (function () use ($index) {
                $phone = '+7901' . str_pad((string) $index, 7, '0', STR_PAD_LEFT);
                $message = 'Seed SMS message #' . $index;
                $sms = 'sms:' . $phone . '?body=' . rawurlencode($message);

                return [$sms, [
                    'phone' => $phone,
                    'message' => $message,
                ]];
            })(),
            'geo' => (function () use ($index) {
                $lat = 40 + ($index % 20) + ($index / 1000);
                $lng = 20 + ($index % 30) + ($index / 1000);

                return ['geo:' . $lat . ',' . $lng, ['lat' => $lat, 'lng' => $lng]];
            })(),
            default => [
                'Seed text QR content #' . $index,
                ['text' => 'Seed text QR content #' . $index],
            ],
        };
    }
}
