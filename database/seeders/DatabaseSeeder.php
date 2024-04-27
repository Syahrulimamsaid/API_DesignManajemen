<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Job;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        

        User::create([
            'kode' => 'USR-2404200001',
            'nama'=> 'Koor',
            'jenis_kelamin'=> 1,
            'username'=>'koor',
            'password'=>bcrypt('12345'),
            'role'=>1
        ]);

        User::create([
            'kode' => 'USR-2404200002',
            'nama'=> 'CS',
            'jenis_kelamin'=> 2,
            'username'=>'cs',
            'password'=>bcrypt('12345'),
            'role'=>2
        ]);

        User::create([
            'kode' => 'USR-2404200003',
            'nama'=> 'QC',
            'jenis_kelamin'=> 1,
            'username'=>'qc',
            'password'=>bcrypt('12345'),
            'role'=>3
        ]);

        User::create([
            'kode' => 'USR-2404200004',
            'nama'=> 'Designer',
            'jenis_kelamin'=> 1,
            'username'=>'designer',
            'password'=>bcrypt('12345'),
            'role'=>4
        ]);

        // Job::create([
        //     'kode' => 'JOB-2403270001',
        //     'nama' => 'ChoChoa',
        //     'Perusahaan' => 'PT Pencari Bakat',
        //     'tanggal_kirim'=> '2024-10-12',
        //     'catatan'=>'Menggunakan warna cerah',
        //     'status'=>1
        // ]);

        // Job::create([
        //     'kode' => 'JOB-2403270002',
        //     'nama' => 'Sayang',
        //     'Perusahaan' => 'PT Pencari Bakat',
        //     'tanggal_kirim'=> '2024-10-12',
        //     'catatan'=>'Menggunakan warna cerah',
        //     'status_data'=>true,
        //     'status'=>2
        // ]);

        // Job::create([
        //     'kode' => 'JOB-2403270003',
        //     'nama' => 'Taro',
        //     'Perusahaan' => 'PT Pencari Bakat',
        //     'tanggal_kirim'=> '2024-10-12',
        //     'catatan'=>'Menggunakan warna cerah',
        //     'status_data'=>true,
        //     'status'=>3
        // ]);

        // Job::create([
        //     'kode' => 'JOB-2403270004',
        //     'nama' => 'Lolipop',
        //     'Perusahaan' => 'PT Pencari Bakat',
        //     'tanggal_kirim'=> '2024-10-12',
        //     'catatan'=>'Menggunakan warna cerah',
        //     'status_data'=>true,
        //     'status'=>6
        // ]);

        // Job::create([
        //     'kode' => 'JOB-2403270005',
        //     'nama' => 'Kuku Bima',
        //     'Perusahaan' => 'PT Pencari Bakat',
        //     'tanggal_kirim'=> '2024-10-12',
        //     'catatan'=>'Menggunakan warna cerah',
        //     'status_data'=>true,
        //     'status'=>4
        // ]);

        // JobAssignment::create([
        //     'kode' => 'JSM-2403270001',
        //     'job_kode'=> 'JOB-2403270001',
        //     'designer_kode'=>'USR-2403270004',
        //     'tanggal_pengumpulan'=>'2024-11-12',
        // ]);

        // JobAssignment::create([
        //     'kode' => 'JSM-2403270002',
        //     'job_kode'=> 'JOB-2403270004',
        //     'designer_kode'=>'USR-2403270004',
        //     'tanggal_pengumpulan'=>'2024-11-12',
        //     'status'=>true
        // ]);

        // JobAssignment::create([
        //     'kode' => 'JSM-2403270003',
        //     'job_kode'=> 'JOB-2403270005',
        //     'designer_kode'=>'USR-2403270004',
        //     'tanggal_pengumpulan'=>'2024-11-12',
        //     'status'=>true
        // ]);
        // Job::factory(5)->create();
        // JobAssignment::factory(3)->create();

    }
}
