<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'income' => fake()->randomFloat(2, 1000, 20000),
            'age' => fake()->numberBetween(18, 70),
            'employment' => fake()->randomElement(['CLT', 'Autônomo', 'Empresário', 'Servidor Público']),
            'credit_history' => fake()->randomElement(['Excelente', 'Bom', 'Regular', 'Ruim']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
