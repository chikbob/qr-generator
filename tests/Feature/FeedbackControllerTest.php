<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_priority_is_derived_from_user_plan(): void
    {
        $plan = Plan::create([
            'name' => 'Enterprise',
            'description' => 'Top tier plan',
            'price' => 29.99,
        ]);
        $user = User::factory()->create(['plan_id' => $plan->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('feedback.store'), [
                'subject' => 'Production issue',
                'message' => 'Need help with an incident',
                'category' => 'bug',
            ]);

        $feedback = Feedback::query()->first();
        $this->assertNotNull($feedback);

        $response
            ->assertRedirect(route('feedback.show', $feedback->id))
            ->assertSessionHas('success', 'flash.feedback.sent');

        $this->assertSame('high', $feedback->priority);
        $this->assertSame($user->id, $feedback->user_id);
    }

    public function test_user_cannot_view_another_users_feedback(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $feedback = Feedback::create([
            'user_id' => $owner->id,
            'subject' => 'Private ticket',
            'message' => 'Hidden from other users',
            'category' => 'general',
            'priority' => 'low',
        ]);

        $this
            ->actingAs($intruder)
            ->get(route('feedback.show', $feedback))
            ->assertForbidden();
    }
}
