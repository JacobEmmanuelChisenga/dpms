<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermitIssuanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_officer_can_complete_issuance_wizard(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);
        $driver = Driver::factory()->create();

        $this->actingAs($officer)
            ->get(route('permits.issue'))
            ->assertOk()
            ->assertSee(__('Step 1: Select driver'));

        $this->post(route('permits.issue.driver'), ['driver_id' => $driver->id])
            ->assertRedirect(route('permits.issue.validity'));

        $issueDate = now()->format('Y-m-d');
        $expiryDate = now()->addYear()->format('Y-m-d');

        $this->post(route('permits.issue.validity.store'), [
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
        ])->assertRedirect(route('permits.issue.review'));

        $this->post(route('permits.issue.review.store'), ['approved' => '1'])
            ->assertRedirect(route('permits.issue.generate'));

        $response = $this->post(route('permits.issue.generate.store'));
        $permit = $driver->fresh()->permits()->first();

        $this->assertNotNull($permit);
        $response->assertRedirect(route('permits.issue.complete', $permit));

        $this->get(route('permits.issue.complete', $permit))
            ->assertOk()
            ->assertSee('data:image/svg+xml;base64,', false);
    }

    public function test_management_cannot_access_issuance_wizard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGEMENT]);

        $this->actingAs($user)->get(route('permits.issue'))->assertForbidden();
    }

    public function test_driver_with_active_permit_is_excluded_from_issuance_list(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);
        $issued = Driver::factory()->create(['full_name' => 'Already Issued']);
        $available = Driver::factory()->create(['full_name' => 'Needs Permit']);

        Permit::factory()->create([
            'driver_id' => $issued->id,
            'issued_by' => $officer->id,
            'status' => Permit::STATUS_VALID,
            'expiry_date' => now()->addMonths(6),
        ]);

        $this->actingAs($officer)
            ->get(route('permits.issue'))
            ->assertOk()
            ->assertSee('Needs Permit')
            ->assertDontSee('Already Issued');

        $this->actingAs($officer)
            ->post(route('permits.issue.driver'), ['driver_id' => $issued->id])
            ->assertSessionHasErrors('driver_id');
    }
}
