<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverAccountLoginRejectedTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_role_accounts_cannot_complete_login(): void
    {
        $driverLogin = User::factory()->create(['role' => User::ROLE_DRIVER]);

        $response = $this->post('/login', [
            'email' => $driverLogin->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_staff_roles_still_login(): void
    {
        $fleet = User::factory()->create(['role' => User::ROLE_FLEET_OFFICER]);

        $response = $this->post('/login', [
            'email' => $fleet->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($fleet);
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
