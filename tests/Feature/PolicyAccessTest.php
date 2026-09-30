<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\University;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PolicyAccessTest extends TestCase
{
    private University $uniA;

    private University $uniB;

    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uniA = University::factory()->create();
        $this->uniB = University::factory()->create();

        foreach (['view', 'create', 'edit', 'delete', 'approve', 'publish', 'archive'] as $action) {
            Permission::findOrCreate("{$action}_policies", 'web');
        }

        $this->adminA = $this->userFor($this->uniA);
    }

    private function userFor(University $university, bool $superAdmin = false): User
    {
        $user = User::factory()->create(['university_id' => $university->id]);
        $user->givePermissionTo(Permission::where('name', 'like', '%_policies')->get());

        if ($superAdmin) {
            $user->assignRole(Role::findOrCreate('super-admin', 'web'));
        }

        return $user;
    }

    private function policy(?int $universityId, string $code = 'HR-LEAVE-001', string $status = 'active'): Policy
    {
        $policy = Policy::create([
            'university_id' => $universityId,
            'code' => $code,
            'title' => "Policy {$code}",
            'category' => 'hr_policy',
            'scope' => 'university-wide',
            'status' => $status,
            'created_by' => $this->adminA->id,
        ]);

        PolicyRule::create([
            'policy_id' => $policy->id, 'key' => 'max_annual_leave', 'value' => '28',
            'type' => 'numeric', 'priority' => 0, 'is_active' => true,
        ]);

        return $policy;
    }

    public function test_a_university_can_read_a_platform_default_but_not_change_it(): void
    {
        $platform = $this->policy(null);

        $this->actingAs($this->adminA)->get(route('policies.show', $platform))->assertOk();
        $this->actingAs($this->adminA)->get(route('policies.edit', $platform))->assertForbidden();
        $this->actingAs($this->adminA)->put(route('policies.update', $platform), ['title' => 'x'])->assertForbidden();
        $this->actingAs($this->adminA)->delete(route('policies.destroy', $platform))->assertForbidden();
        $this->actingAs($this->adminA)->post(route('policies.publish', $platform))->assertForbidden();
        $this->actingAs($this->adminA)->get(route('policies.rules.create', $platform))->assertForbidden();

        $this->assertNull($platform->fresh()->deleted_at);
        $this->assertSame('Policy HR-LEAVE-001', $platform->fresh()->title);
    }

    public function test_another_universitys_policy_does_not_exist_for_this_university(): void
    {
        $theirs = $this->policy($this->uniB->id);

        $this->actingAs($this->adminA)->get(route('policies.show', $theirs))->assertNotFound();
        $this->actingAs($this->adminA)->get(route('policies.edit', $theirs))->assertNotFound();
        $this->actingAs($this->adminA)->get(route('policies.versions', $theirs))->assertNotFound();
    }

    public function test_a_university_can_edit_its_own_policy(): void
    {
        $own = $this->policy($this->uniA->id);

        $this->actingAs($this->adminA)->get(route('policies.edit', $own))->assertOk();
        $this->actingAs($this->adminA)->get(route('policies.rules.create', $own))->assertOk();
    }

    public function test_a_super_admin_can_manage_platform_defaults(): void
    {
        $platform = $this->policy(null);

        $this->actingAs($this->userFor($this->uniA, superAdmin: true))
            ->get(route('policies.edit', $platform))
            ->assertOk();
    }

    public function test_a_platform_default_offers_customize_instead_of_edit_actions(): void
    {
        $platform = $this->policy(null);

        $this->actingAs($this->adminA)->get(route('policies.show', $platform))
            ->assertOk()
            ->assertSee('Platform default')
            ->assertSee('Customize for my university')
            ->assertDontSee(route('policies.edit', $platform))
            ->assertDontSee(route('policies.rules.index', $platform))
            ->assertDontSee('Approve &amp; Sign', false);
    }

    public function test_after_customizing_the_platform_page_points_to_the_universitys_copy(): void
    {
        $platform = $this->policy(null);
        $this->actingAs($this->adminA)->post(route('policies.customize', $platform));
        $copy = Policy::where('university_id', $this->uniA->id)->firstOrFail();

        $this->actingAs($this->adminA)->get(route('policies.show', $platform))
            ->assertOk()
            ->assertSee("Open my university's copy", false)
            ->assertDontSee('Customize for my university');

        $this->actingAs($this->adminA)->get(route('policies.show', $copy))
            ->assertOk()
            ->assertSee(route('policies.edit', $copy))
            ->assertDontSee('Customize for my university');
    }

    public function test_the_index_lists_own_and_platform_policies_but_never_another_universitys(): void
    {
        $this->policy(null, 'PLATFORM-1');
        $this->policy($this->uniA->id, 'OWN-1');
        $this->policy($this->uniB->id, 'THEIRS-1');

        $this->actingAs($this->adminA)->get(route('policies.index'))
            ->assertOk()
            ->assertSee('PLATFORM-1')
            ->assertSee('OWN-1')
            ->assertDontSee('THEIRS-1');
    }

    public function test_customizing_a_platform_default_creates_an_unpublished_copy_with_its_rules(): void
    {
        $platform = $this->policy(null);
        $platform->update(['chairperson_signature_path' => 'sig.png', 'approved_at' => now(), 'effective_date' => now()->subDay()]);

        $response = $this->actingAs($this->adminA)->post(route('policies.customize', $platform));

        $copy = Policy::where('university_id', $this->uniA->id)->where('code', 'HR-LEAVE-001')->firstOrFail();
        $response->assertRedirect(route('policies.show', $copy));

        $this->assertSame(Policy::STATUS_DRAFT, $copy->status);
        $this->assertSame(1, $copy->version);
        $this->assertSame($this->adminA->id, $copy->created_by);
        $this->assertNull($copy->approved_at);
        $this->assertNull($copy->chairperson_signature_path);
        $this->assertNull($copy->effective_date);
        $this->assertSame(['max_annual_leave' => '28'], $copy->rules()->pluck('value', 'key')->all());

        // The platform default is untouched and still what applies until the copy is published.
        $this->assertSame(Policy::STATUS_ACTIVE, $platform->fresh()->status);
        $this->assertSame(1, $platform->fresh()->rules()->count());
    }

    public function test_customizing_twice_does_not_create_a_second_copy(): void
    {
        $platform = $this->policy(null);

        $this->actingAs($this->adminA)->post(route('policies.customize', $platform));
        $this->actingAs($this->adminA)->post(route('policies.customize', $platform));

        $this->assertSame(1, Policy::where('university_id', $this->uniA->id)->where('code', 'HR-LEAVE-001')->count());
    }

    public function test_customizing_after_deleting_the_copy_restores_it_instead_of_failing(): void
    {
        $platform = $this->policy(null);

        $this->actingAs($this->adminA)->post(route('policies.customize', $platform));
        $copy = Policy::where('university_id', $this->uniA->id)->firstOrFail();
        $copy->delete();

        $this->actingAs($this->adminA)->post(route('policies.customize', $platform))
            ->assertRedirect(route('policies.show', $copy));

        $this->assertNull($copy->fresh()->deleted_at);
        $this->assertSame(1, Policy::withTrashed()->where('university_id', $this->uniA->id)->where('code', 'HR-LEAVE-001')->count());
    }

    public function test_only_a_platform_default_can_be_customized(): void
    {
        $own = $this->policy($this->uniA->id);

        $this->actingAs($this->adminA)->post(route('policies.customize', $own))->assertForbidden();
    }

    public function test_a_rule_cannot_be_reached_through_a_different_policys_url(): void
    {
        $own = $this->policy($this->uniA->id, 'OWN-1');
        $other = $this->policy($this->uniA->id, 'OWN-2');
        $otherRule = $other->rules()->first();

        $this->actingAs($this->adminA)
            ->get(route('policies.rules.edit', [$own, $otherRule]))
            ->assertNotFound();
    }

    public function test_a_code_may_repeat_across_universities_but_not_within_one(): void
    {
        $this->policy(null, 'HANDBOOK-1');
        $payload = ['code' => 'HANDBOOK-1', 'title' => 'Own handbook', 'category' => 'hr_policy', 'scope' => 'university-wide'];

        $this->actingAs($this->adminA)->post(route('policies.store'), $payload)->assertSessionHasNoErrors();
        $this->assertSame(1, Policy::where('university_id', $this->uniA->id)->where('code', 'HANDBOOK-1')->count());

        $this->actingAs($this->adminA)->post(route('policies.store'), $payload)->assertSessionHasErrors('code');
        $this->assertSame(1, Policy::where('university_id', $this->uniA->id)->where('code', 'HANDBOOK-1')->count());
    }

    public function test_only_a_super_admin_can_create_a_platform_default(): void
    {
        $payload = ['code' => 'PLAT-1', 'title' => 'Platform', 'category' => 'hr_policy', 'scope' => 'university-wide', 'is_platform' => '1'];

        $this->actingAs($this->adminA)->post(route('policies.store'), $payload);
        $this->assertNull(Policy::where('code', 'PLAT-1')->whereNull('university_id')->first());
        $this->assertNotNull(Policy::where('code', 'PLAT-1')->where('university_id', $this->uniA->id)->first());

        $this->actingAs($this->userFor($this->uniA, superAdmin: true))
            ->post(route('policies.store'), ['code' => 'PLAT-2'] + $payload);
        $this->assertNotNull(Policy::where('code', 'PLAT-2')->whereNull('university_id')->first());
    }
}
