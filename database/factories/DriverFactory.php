<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'employee_id' => 'ZFF-'.fake()->unique()->numerify('####'),
            'full_name' => fake()->name(),
            'nrc' => fake()->numerify('######/##/#'),
            'department' => fake()->randomElement(['Transport', 'Forestry', 'Operations']),
            'license_number' => 'DL-'.fake()->numerify('####'),
            'license_class' => fake()->randomElement(['B', 'C', 'CE']),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
