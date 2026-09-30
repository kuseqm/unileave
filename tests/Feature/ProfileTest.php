<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->from('/profile')
            ->actingAs($user)
            ->put('/profile', [
                'gender' => 'male',
                'marital_status' => 'single',
                'phone' => '0712345678',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('male', $user->gender);
        $this->assertSame('single', $user->marital_status);
        $this->assertSame('0712345678', $user->phone);
    }
}
