<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleetOfficerTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_officer_sees_operational_dashboard(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($officer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Fleet operations — permits, drivers, and renewals at a glance.'))
            ->assertSee(__('Expiring Soon'))
            ->assertDontSee(__('Total Users'));
    }

    public function test_fleet_officer_can_open_renewals_workspace(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($officer)
            ->get(route('permits.renewals.index'))
            ->assertOk()
            ->assertSee(__('Renewals'));
    }

    public function test_fleet_officer_cannot_access_archives(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($officer)->get(route('archives.index'))->assertForbidden();
    }

    public function test_fleet_officer_cannot_access_settings(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($officer)->get(route('settings.index'))->assertForbidden();
    }

    public function test_management_sees_read_only_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGEMENT]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Read-only access'))
            ->assertDontSee(__('Issue New Permit'));
    }

    public function test_admin_still_sees_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('Here is an overview of the Driver Permit Management System.'));
    }
}
