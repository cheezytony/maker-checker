<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $admin = $this->faker->unique();
        return [
            'first_name' => $admin->firstName,
            'last_name' => $admin->lastName,
            'email' => $admin->companyEmail,
            'password' => 'password',
        ];
    }
}
