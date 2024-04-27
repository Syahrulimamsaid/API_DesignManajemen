<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name,
            'perusahaan' => fake()->name,
            'status' => Arr::random(['Terjadwal', 'Pengerjaan', 'Belum Terjadwal', 'Revisi', 'Selesai']),
            'tanggal_kirim' => fake()->date,
            'catatan' => fake()->paragraph(5, 15),
            'tanggapan_customer' => Arr::random(['Diterima', 'Ditolak', ''])
        ];
    }
}
