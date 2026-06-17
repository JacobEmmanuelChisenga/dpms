<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_user_directory(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_fleet_officer_cannot_open_account_administration(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($user)->get(route('archives.index'))->assertForbidden();
    }

    public function test_admin_can_review_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('audit-logs.index'))->assertOk();
    }

    public function test_management_cannot_review_audit_logs(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGEMENT]);

        $this->actingAs($user)->get(route('audit-logs.index'))->assertForbidden();
    }
}
