<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'name' => 'umum_'.fake()->unique()->numberBetween(1, 99999),
            'type' => 'umum',
            'plate_number' => strtoupper(fake()->unique()->bothify('B #### ???')),
            'is_active' => true,
        ];
    }

    public function ambulance(): static
    {
        return $this->state(fn () => [
            'name' => 'ambulance_'.fake()->unique()->numberBetween(1, 99999),
            'type' => 'ambulance',
        ]);
    }
}
