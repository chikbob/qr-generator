<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class LocalizedDatabaseSeeder extends Seeder
{
    private ?array $resolvedSeedContent = null;

    /**
     * @return array<string, mixed>
     */
    abstract protected function seedContent(): array;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedPlans();

            $planIds = Plan::query()->pluck('id', 'name')->all();

            $this->seedUsers($planIds);

            $users = User::query()->orderBy('id')->get();

            $this->seedQrCodes($users);

            $qrCodes = QrCode::query()->orderBy('id')->get();

            $this->seedQrScans($qrCodes);
            $this->refreshScanCounts($qrCodes);
            $this->seedFeedback($users);
        });
    }

    private function content(string $key): mixed
    {
        if ($this->resolvedSeedContent === null) {
            $this->resolvedSeedContent = $this->seedContent();
        }

        return data_get($this->resolvedSeedContent, $key);
    }

    private function seedPlans(): void
    {
        foreach ($this->content('plans') as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                [
                    'description' => $plan['description'],
                    'price' => $plan['price'],
                    'is_default' => $plan['is_default'],
                ]
            );
        }
    }

    /**
     * @param array<string, int> $planIds
     */
    private function seedUsers(array $planIds): void
    {
        $fixedUsers = [
            [
                'email' => 'liza@gmail.com',
                'name' => $this->content('users.liza_name'),
                'password' => bcrypt('password'),
                'plan_id' => $planIds['Free'],
                'is_admin' => false,
            ],
            [
                'email' => 'admin@gmail.com',
                'name' => $this->content('users.admin_name'),
                'password' => bcrypt('password'),
                'plan_id' => $planIds['Enterprise'],
                'is_admin' => true,
            ],
            [
                'email' => 'user@gmail.com',
                'name' => $this->content('users.user_name'),
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
                    'name' => sprintf($this->content('users.seed_user_name'), $i),
                    'password' => bcrypt('password'),
                    'plan_id' => $planRotation[($i - 1) % count($planRotation)],
                    'is_admin' => false,
                ]
            );
        }
    }

    /**
     * @param Collection<int, User> $users
     */
    private function seedQrCodes(Collection $users): void
    {
        $types = ['text', 'url', 'email', 'tel', 'sms', 'geo'];
        $urlHosts = $this->content('qr.url_hosts');
        $sizes = [180, 200, 220, 260, 300];
        $darkColors = ['#000000', '#1f2937', '#0f766e', '#7c2d12'];

        for ($i = 0; $i < 90; $i++) {
            $index = $i + 1;
            $type = $types[$i % count($types)];
            $isDynamicAllowed = in_array($type, ['url', 'email', 'tel', 'sms', 'geo'], true);
            $isDynamic = $isDynamicAllowed && $i % 3 !== 0;

            [$content, $payload] = $this->buildQrData($type, $urlHosts, $index);

            QrCode::updateOrCreate(
                ['image_path' => 'qr_codes/seed-' . $index . '.png'],
                [
                    'user_id' => $users[$i % $users->count()]->id,
                    'type' => $type,
                    'content' => $content,
                    'payload' => $payload,
                    'size' => $sizes[$i % count($sizes)],
                    'color_dark' => $darkColors[$i % count($darkColors)],
                    'color_light' => '#ffffff',
                    'is_dynamic' => $isDynamic,
                    'slug' => $isDynamic ? sprintf('seed-qr-%03d', $index) : null,
                ]
            );
        }
    }

    /**
     * @param Collection<int, QrCode> $qrCodes
     */
    private function seedQrScans(Collection $qrCodes): void
    {
        $countryCityMap = $this->content('scans.country_city_map');
        $weightedCountries = $this->content('scans.weighted_countries');
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

            QrScan::updateOrCreate(
                [
                    'qr_code_id' => $qrCodes[$i % $qrCodes->count()]->id,
                    'ip' => sprintf(
                        '10.%d.%d.%d',
                        ($i % 250) + 1,
                        (($i * 3) % 250) + 1,
                        (($i * 7) % 250) + 1
                    ),
                ],
                [
                    'country' => $country,
                    'city' => $city,
                    'user_agent' => sprintf('SeedAgent/%d.0', ($i % 5) + 1),
                    'device' => $devices[$i % count($devices)],
                    'browser' => $browsers[$i % count($browsers)],
                    'referer' => $referers[$i % count($referers)],
                ]
            );
        }
    }

    /**
     * @param Collection<int, QrCode> $qrCodes
     */
    private function refreshScanCounts(Collection $qrCodes): void
    {
        foreach ($qrCodes as $qrCode) {
            $qrCode->update(['scan_count' => $qrCode->scans()->count()]);
        }
    }

    /**
     * @param Collection<int, User> $users
     */
    private function seedFeedback(Collection $users): void
    {
        $categories = ['general', 'bug', 'idea', 'payment'];
        $statuses = ['new', 'in_progress', 'resolved'];
        $priorities = ['low', 'medium', 'high'];

        for ($i = 0; $i < 70; $i++) {
            $index = $i + 1;

            Feedback::updateOrCreate(
                [
                    'subject' => sprintf($this->content('feedback.subject'), $index),
                ],
                [
                    'user_id' => $users[$i % $users->count()]->id,
                    'message' => sprintf($this->content('feedback.message'), $index),
                    'category' => $categories[$i % count($categories)],
                    'status' => $statuses[$i % count($statuses)],
                    'priority' => $priorities[$i % count($priorities)],
                ]
            );
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
                $subject = sprintf($this->content('qr.email_subject'), $index);
                $body = sprintf($this->content('qr.email_body'), $index);
                $mailto = 'mailto:' . $email . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body);

                return [
                    $mailto,
                    [
                        'email' => $email,
                        'subject' => $subject,
                        'body' => $body,
                    ],
                ];
            })(),

            'tel' => (function () use ($index) {
                $phone = $this->content('qr.phone_prefix') . str_pad((string) $index, 7, '0', STR_PAD_LEFT);

                return ['tel:' . $phone, ['phone' => $phone]];
            })(),

            'sms' => (function () use ($index) {
                $phone = $this->content('qr.sms_phone_prefix') . str_pad((string) $index, 7, '0', STR_PAD_LEFT);
                $message = sprintf($this->content('qr.sms_message'), $index);
                $sms = 'sms:' . $phone . '?body=' . rawurlencode($message);

                return [
                    $sms,
                    [
                        'phone' => $phone,
                        'message' => $message,
                    ],
                ];
            })(),

            'geo' => (function () use ($index) {
                $lat = 40 + ($index % 20) + ($index / 1000);
                $lng = 20 + ($index % 30) + ($index / 1000);

                return ['geo:' . $lat . ',' . $lng, ['lat' => $lat, 'lng' => $lng]];
            })(),

            default => [
                sprintf($this->content('qr.text_content'), $index),
                ['text' => sprintf($this->content('qr.text_content'), $index)],
            ],
        };
    }
}
