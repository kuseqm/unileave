<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordChangeRequiredTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_password_change_required_is_redirected_away_from_normal_pages(): void
    {
        $user = User::factory()->create(['password_change_required' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('profile.show'));
    }

    public function test_user_with_password_change_required_can_still_reach_the_profile_page(): void
    {
        $user = User::factory()->create(['password_change_required' => true]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_user_who_has_changed_their_password_is_not_redirected(): void
    {
        $user = User::factory()->create(['password_change_required' => false]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }
}
