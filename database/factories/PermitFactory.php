<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permit>
 */
class PermitFactory extends Factory
{
    protected $model = Permit::class;

    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'permit_number' => 'DPMS-'.now()->format('Y').'-'.fake()->unique()->numerify('####'),
            'issue_date' => now()->subMonth(),
            'expiry_date' => now()->addYear(),
            'status' => Permit::STATUS_VALID,
            'issued_by' => User::factory()->state(['role' => User::ROLE_FLEET_OFFICER]),
            'qr_code' => null,
        ];
    }
}
