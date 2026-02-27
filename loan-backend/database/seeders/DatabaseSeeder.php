<?php

namespace Database\Seeders;

use App\Models\{Client,LoanApplication};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        Client::factory(10)
            ->has(LoanApplication::factory()->count(2))
            ->create();
    }
}
