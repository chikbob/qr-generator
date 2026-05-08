<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QrCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_user_cannot_create_dynamic_qr_code(): void
    {
        $plan = Plan::create([
            'name' => 'Free',
            'description' => 'Default plan',
            'price' => 0,
            'is_default' => true,
        ]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        $response = $this
            ->actingAs($user)
            ->from(route('history'))
            ->post(route('qr.store'), [
                'type' => 'url',
                'content' => 'https://example.com',
                'size' => 300,
                'color_dark' => '#000000',
                'color_light' => '#ffffff',
                'is_dynamic' => true,
            ]);

        $response
            ->assertRedirect(route('history'))
            ->assertSessionHas('error', 'flash.qr.dynamic_only_pro');

        $this->assertDatabaseCount('qr_codes', 0);
    }

    public function test_dynamic_qr_redirect_tracks_scan_and_redirects_to_target(): void
    {
        Http::fake([
            'https://ipinfo.io/*' => Http::response([
                'country' => 'US',
                'city' => 'New York',
            ], 200),
        ]);

        $user = User::factory()->create();
        $qrCode = QrCode::create([
            'user_id' => $user->id,
            'type' => 'url',
            'content' => 'example.com',
            'image_path' => 'qr_codes/test.png',
            'size' => 300,
            'color_dark' => '#000000',
            'color_light' => '#ffffff',
            'is_dynamic' => true,
            'slug' => 'demo-slug',
        ]);

        $response = $this
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
                'Referer' => 'https://referrer.test/page',
                'X-Forwarded-For' => '8.8.8.8',
            ])
            ->get(route('qr.redirect', $qrCode->slug));

        $response->assertRedirect('https://example.com');

        $scan = QrScan::query()->first();

        $this->assertNotNull($scan);
        $this->assertSame($qrCode->id, $scan->qr_code_id);
        $this->assertSame('8.8.8.8', $scan->ip);
        $this->assertSame('US', $scan->country);
        $this->assertSame('New York', $scan->city);
        $this->assertSame('https://referrer.test/page', $scan->referer);
    }

    public function test_plain_text_dynamic_qr_returns_text_response_instead_of_redirect(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $qrCode = QrCode::create([
            'user_id' => $user->id,
            'type' => 'text',
            'content' => 'plain text payload',
            'image_path' => 'qr_codes/test.png',
            'size' => 300,
            'color_dark' => '#000000',
            'color_light' => '#ffffff',
            'is_dynamic' => true,
            'slug' => 'text-slug',
        ]);

        $response = $this->get(route('qr.redirect', $qrCode->slug));

        $response
            ->assertOk()
            ->assertSeeText('plain text payload');

        $this->assertDatabaseCount('qr_scans', 1);
    }
}
