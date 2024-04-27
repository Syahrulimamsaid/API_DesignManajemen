<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobAssignment>
 */
class JobAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => fake()->numberBetween(1, 5),
            'designer_id'=>fake()->numberBetween(1, 4),
            'tanggal_pengumpulan'=>fake()->date,
            'status' => Arr::random(['Diterima','Belum Diterima'])
        ];
    }
}
