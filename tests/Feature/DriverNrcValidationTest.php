<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverNrcValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_registration_requires_valid_nrc_format(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $this->actingAs($officer)
            ->post(route('drivers.store'), [
                'employee_id' => 'ZFF-3001',
                'full_name' => 'Test Driver',
                'nrc' => '123456/78',
                'department' => 'Transport',
                'license_number' => 'DL-1',
                'license_class' => 'B',
                'phone' => '+260 97 111 1111',
            ])
            ->assertSessionHasErrors('nrc');

        $this->actingAs($officer)
            ->post(route('drivers.store'), [
                'employee_id' => 'ZFF-3001',
                'full_name' => 'Test Driver',
                'nrc' => '123456781',
                'department' => 'Transport',
                'license_number' => 'DL-1',
                'license_class' => 'B',
                'phone' => '+260 97 111 1111',
            ])
            ->assertRedirect(route('drivers.index'));
    }
}
