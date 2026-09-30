<?php

namespace Tests\Feature;

use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FlashMessageSurvivesBackgroundRequestsTest extends TestCase
{
    use RefreshDatabase;

    private function userWhoCanOpenTheCatalog(): User
    {
        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'import_students', 'guard_name' => 'web']);

        $university = University::factory()->create(['enabled_addons' => ['academic_records']]);
        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(['view_students', 'import_students']);

        return $user;
    }

    /** The redirect after a save flashes "success"; the next real page must still be able to show it. */
    private function flashSuccess(): array
    {
        return ['success' => 'Saved for the test.', '_flash' => ['old' => ['success'], 'new' => []]];
    }

    public function test_a_background_poll_between_the_redirect_and_the_page_does_not_use_up_the_message(): void
    {
        $user = $this->userWhoCanOpenTheCatalog();

        $this->actingAs($user)->withSession($this->flashSuccess());

        $this->withHeaders(['Sec-Fetch-Dest' => 'empty'])->get('/notifications/unread-count');
        $this->withHeaders(['Sec-Fetch-Dest' => 'empty'])->get('/messages/unread-count');

        $this->get(route('students.catalog.index'))
            ->assertOk()
            ->assertSee('Saved for the test.');
    }

    public function test_the_message_is_still_shown_only_once_on_real_page_loads(): void
    {
        $user = $this->userWhoCanOpenTheCatalog();

        $this->actingAs($user)->withSession($this->flashSuccess());

        $this->get(route('students.catalog.index'))->assertSee('Saved for the test.');
        $this->get(route('students.catalog.index'))->assertDontSee('Saved for the test.');
    }
}
