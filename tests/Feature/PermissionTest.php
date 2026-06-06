<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_manager_cannot_access_admin_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_team_member_cannot_access_admin_dashboard(): void
    {
        $member = User::factory()->teamMember()->create();

        $response = $this->actingAs($member)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get(route('manager.dashboard'));

        $response->assertOk();
    }

    public function test_team_member_cannot_access_manager_dashboard(): void
    {
        $member = User::factory()->teamMember()->create();

        $response = $this->actingAs($member)->get(route('manager.dashboard'));

        $response->assertForbidden();
    }

    public function test_team_member_can_access_member_dashboard(): void
    {
        $member = User::factory()->teamMember()->create();

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertOk();
    }

    public function test_manager_cannot_access_member_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get(route('member.dashboard'));

        $response->assertForbidden();
    }
}
