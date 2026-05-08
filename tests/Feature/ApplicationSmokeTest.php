<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_available(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('scan'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_health_endpoint_returns_expected_payload(): void
    {
        $this->get(route('health'))
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'timestamp',
            ])
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get(route('generate'))->assertRedirect(route('login'));
        $this->get(route('history'))->assertRedirect(route('login'));
        $this->get(route('plans.index'))->assertRedirect(route('login'));
        $this->get(route('feedback.index'))->assertRedirect(route('login'));
        $this->get(route('contacts'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_main_internal_pages(): void
    {
        $plan = Plan::create([
            'name' => 'Pro',
            'description' => 'Paid plan',
            'price' => 9.99,
        ]);
        $user = User::factory()->create([
            'plan_id' => $plan->id,
        ]);

        $this->actingAs($user)->get(route('generate'))->assertOk();
        $this->actingAs($user)->get(route('history'))->assertOk();
        $this->actingAs($user)->get(route('profile.show'))->assertOk();
        $this->actingAs($user)->get(route('plans.index'))->assertOk();
        $this->actingAs($user)->get(route('contacts'))->assertOk();
        $this->actingAs($user)->get(route('feedback.create'))->assertOk();
    }
}
