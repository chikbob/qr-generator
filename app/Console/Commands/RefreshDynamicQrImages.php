<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use Illuminate\Console\Command;

class RefreshDynamicQrImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:refresh-dynamic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate PNG files for all dynamic QR codes using current public base URL';

    public function handle(): int
    {
        $baseUrl = rtrim((string) (config('app.public_url') ?: config('app.url')), '/');
        if ($baseUrl === '') {
            $this->error('Missing base URL. Set QR_PUBLIC_BASE_URL or APP_URL first.');
            return self::FAILURE;
        }

        $codes = QrCode::query()
            ->where('is_dynamic', true)
            ->whereNotNull('slug')
            ->get();

        if ($codes->isEmpty()) {
            $this->info('No dynamic QR codes found.');
            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($codes as $code) {
            $path = (string) $code->image_path;
            if ($path === '') {
                continue;
            }

            $fullPath = public_path($path);
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $dark = $this->parseHexColor((string) ($code->color_dark ?? '#000000'), [0, 0, 0]);
            $light = $this->parseHexColor((string) ($code->color_light ?? '#ffffff'), [255, 255, 255]);
            $size = (int) ($code->size ?: 300);

            $qrContent = $baseUrl . '/r/' . $code->slug;

            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->encoding('UTF-8')
                ->size($size)
                ->color(...$dark)
                ->backgroundColor(...$light)
                ->generate($qrContent, $fullPath);

            $updated++;
        }

        $this->info("Regenerated dynamic QR images: {$updated}");

        return self::SUCCESS;
    }

    /**
     * @param array<int, int> $fallback
     * @return array<int, int>
     */
    private function parseHexColor(string $hex, array $fallback): array
    {
        $hex = trim($hex);
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
            return $fallback;
        }

        $parts = sscanf($hex, '#%02x%02x%02x');
        if (!is_array($parts) || count($parts) !== 3) {
            return $fallback;
        }

        return array_map(fn($v) => (int) $v, $parts);
    }
}
