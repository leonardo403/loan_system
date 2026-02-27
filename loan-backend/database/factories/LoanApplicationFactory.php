<?php

namespace Database\Factories;

use App\Models\{Client,LoanApplication};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanApplication>
 */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'requested_amount' => fake()->randomFloat(2, 5000, 100000),
            'purpose' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
