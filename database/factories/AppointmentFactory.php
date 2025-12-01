<?php

namespace Database\Factories;

use App\Models\MedicalCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'medical_center_id' => MedicalCenter::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'scheduled_at' => now()->addDays(1),
            'notes' => fake()->sentence(),
        ];
    }
}
