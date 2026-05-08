<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_subscribe_to_a_plan(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'name' => 'Pro',
            'description' => 'Paid plan',
            'price' => 9.99,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('plans.subscribe'), [
                'plan_id' => $plan->id,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'flash.plan.updated');

        $this->assertSame($plan->id, $user->fresh()->plan_id);
    }

    public function test_payment_route_assigns_selected_plan_to_user(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'name' => 'Enterprise',
            'description' => 'Top tier plan',
            'price' => 29.99,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('plans.pay', $plan), [
                'card_number' => '4242424242424242',
                'expiry_date' => '12/30',
                'cvv' => '123',
            ]);

        $response
            ->assertRedirect(route('plans.index'))
            ->assertSessionHas('success', 'flash.plan.payment_success');

        $this->assertSame($plan->id, $user->fresh()->plan_id);
    }
}
