<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Job;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\TimeLines;
use App\Models\DataPendukung;
use App\Models\JobAssignment;
use App\Models\QualityControl;
use Illuminate\Database\Seeder;
use PHPUnit\Framework\Constraint\IsTrue;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::create([
            'kode' => 'USR-24040001',
            'nama' => 'Yanto Jayadi',
            'jenis_kelamin' => 1,
            'username' => 'koor',
            'password' => bcrypt('12345'),
            'role' => 1
        ]);

        User::create([
            'kode' => 'USR-24040002',
            'nama' => 'Susi Andriani',
            'jenis_kelamin' => 2,
            'username' => 'cs',
            'password' => bcrypt('12345'),
            'role' => 2
        ]);

        User::create([
            'kode' => 'USR-24040003',
            'nama' => 'Adib Bagaskara',
            'jenis_kelamin' => 1,
            'username' => 'qc',
            'password' => bcrypt('12345'),
            'role' => 3
        ]);

        User::create([
            'kode' => 'USR-24040004',
            'nama' => 'Firmansyah',
            'jenis_kelamin' => 1,
            'username' => 'designer',
            'password' => bcrypt('12345'),
            'role' => 4
        ]);

        User::create([
            'kode' => 'USR-24040005',
            'nama' => 'Sunjoyo',
            'jenis_kelamin' => 1,
            'username' => 'designer1',
            'password' => bcrypt('12345'),
            'role' => 4
        ]);

        User::create([
            'kode' => 'USR-24040006',
            'nama' => 'Putri Cantika',
            'jenis_kelamin' => 2,
            'username' => 'designer2',
            'password' => bcrypt('12345'),
            'role' => 4
        ]);

        //Job 1 || Acc + Acc + Final
        Job::create([
            'kode' => 'JOB-24050001',
            'nama' => 'KANTONG MARDY F1',
            'Perusahaan' => 'PT BENIH CITRA ASIA',
            'tanggal_kirim' => '2024-05-11',
            'catatan' => 'Menggunakan warna cerah',
            'status' => 5,
            'status_data' => 1,
            'tanggapan_customer' => 1,
            'hasil_design' => 'design_KANTONG MARDY F1240505.png'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050001',
            'nama' => 'data_kantong_mardy_f1(1).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050001',
            'nama' => 'data_kantong_mardy_f1(2).pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050001',
            'job_kode' => 'JOB-24050001',
            'designer_kode' => 'USR-24040004',
            'tanggal_pengumpulan' => '2024-05-10',
            'status' => true
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050001',
            'job_assignment_kode' => 'JSM-24050001',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050002',
            'job_assignment_kode' => 'JSM-24050001',
            'petugas_kode' => 'USR-24040001',
            'status' => 0,
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050001',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-08',
            'job_assignment_kode' => 'JSM-24050001',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050001',
            'mulai_pengerjaan' => '2024-05-06', 'selesai_pengerjaan' => '2024-05-08',
        ]);

        TimeLines::create([
            'event' => 'Lolos QC', 'tanggal_event' => '2024-05-09',
            'quality_control_kode' =>  'QCL-24050001',
            'job_assignment_kode' => 'JSM-24050001',
        ]);

        TimeLines::create([
            'event' => 'Lolos Koordinator', 'tanggal_event' => '2024-05-09',
            'quality_control_kode' =>  'QCL-24050002',
            'job_assignment_kode' => 'JSM-24050001',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050018',
            'job_assignment_kode' => 'JSM-24050001',
            'petugas_kode' => 'USR-24040001',
            'status' => 1,
            'komentar' => 'Warna terlalu gelap pada bagian Background.'
        ]);

        TimeLines::create([
            'event' => 'Pekerjaan ditolak Customer', 'tanggal_event' => '2024-05-11',
            'job_assignment_kode' => 'JSM-24050001', 'quality_control_kode' => 'QCL-24050018'
        ]);

        TimeLines::create([
            'event' => 'Penjadwalan Revisi 1', 'tanggal_event' => '2024-05-12',
            'job_assignment_kode' => 'JSM-24050001',
        ]);

        // Job::create([
        //     'kode' => 'JOB-24050002',
        //     'nama' => 'FARGETIX KAPLET',
        //     'Perusahaan' => 'PT IFARS PHARMACEUTICAL LABORATORIES',
        //     'tanggal_kirim' => '2024-05-20',
        //     'catatan' => 'Menggunakan warna cerah',
        //     'status' => 6,
        //     'tanggapan_customer' => 2,
        //     'tanggal_diterima' => '2024-05-20',
        //     'hasil_design' => 'design_fargetix_kaplet.png'
        // ]);

        // JobAssignment::create([
        //     'kode' => 'JSM-24050002',
        //     'job_kode' => 'JOB-24050002',
        //     'designer_kode' => 'USR-24040005',
        //     'tanggal_pengumpulan' => '2024-05-12',
        //     'status' => true
        // ]);

        // QualityControl::create([
        //     'kode' => 'QCL-24050003',
        //     'job_assignment_kode' => 'JSM-24050002',
        //     'petugas_kode' => 'USR-24040003',
        //     'status' => 0,
        // ]);

        // QualityControl::create([
        //     'kode' => 'QCL-24050004',
        //     'job_assignment_kode' => 'JSM-24050002',
        //     'petugas_kode' => 'USR-24040001',
        //     'komentar'=>'Terdapat element yang terlewat',
        //     'status' => 1,
        // ]);

        // QualityControl::create([
        //     'kode' => 'QCL-24050005',
        //     'job_assignment_kode' => 'JSM-24050002',
        //     'petugas_kode' => 'USR-24040003',
        //     'status' => 0,
        // ]);

        // QualityControl::create([
        //     'kode' => 'QCL-24050006',
        //     'job_assignment_kode' => 'JSM-24050002',
        //     'petugas_kode' => 'USR-24040001',
        //     'status' => 0,
        // ]);

        // TimeLines::create([
        //     'event' => 'Mulai Pengerjaan',
        //     'tanggal_event' => '2024-05-05',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Pengumpulan',
        //     'tanggal_event' => '2024-05-12',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Waktu Pengerjaan',
        //     'tanggal_event' => '2024-05-05',
        //     'mulai_pengerjaan'=>'2024-05-05',
        //     'selesai_pengerjaan'=>'2024-05-12',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Lolos QC',
        //     'tanggal_event' => '2024-05-13',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Tolak Koordinator',
        //     'tanggal_event' => '2024-05-14',
        //     'quality_control_kode' =>  'QCL-24050004',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Penjadwalan Revisi 1',
        //     'tanggal_event' => '2024-05-15',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Pengumpulan Revisi 1',
        //     'tanggal_event' => '2024-05-18',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Waktu Pengerjaan',
        //     'tanggal_event' => '2024-05-15',
        //     'mulai_pengerjaan'=>'2024-05-15',
        //     'selesai_pengerjaan'=>'2024-05-18',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Lolos QC',
        //     'tanggal_event' => '2024-05-20',
        //     'quality_control_kode' =>  'QCL-24050005',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Lolos Koordinator',
        //     'tanggal_event' => '2024-05-24',
        //     'quality_control_kode' =>  'QCL-24050006',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        // TimeLines::create([
        //     'event' => 'Pekerjaan diterima Customer',
        //     'tanggal_event' => '2024-05-28',
        //     'job_assignment_kode' => 'JSM-24050002',
        // ]);

        //Job 2 || Plot + Belum Diambil
        Job::create([
            'kode' => 'JOB-24050002',
            'nama' => 'KANTONG JAGUNG MANIS',
            'Perusahaan' => 'PT BENIH CITRA ASIA',
            'tanggal_kirim' => '2024-05-20',
            'catatan' => 'Menggunakan warna cerah',
            'status' => 2,
            'status_data' => 1,
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050002',
            'nama' => 'data_kantong_jagung_manis.pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050002',
            'job_kode' => 'JOB-24050002',
            'designer_kode' => 'USR-24040004',
            'tanggal_pengumpulan' => '2024-05-12',
            'status' => false
        ]);

        //Job 3 || Acc + Reject + Kerja
        Job::create([
            'kode' => 'JOB-24050003',
            'nama' => 'KANTONG BUNGO F1',
            'perusahaan' => 'PT BENIH CITRA ASIA',
            'tanggal_kirim' => '2024-05-20',
            'status_data' => true,
            'catatan' => 'Gambar Terong Ungu yang Rimbun.',
            'status' => 5,
            'hasil_design' => 'design_KANTONG BUNGO F1240506.png'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050003',
            'nama' => 'data_kantong_bungo_f1(1).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050003',
            'nama' => 'data_kantong_bungo_f1(2).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050003',
            'nama' => 'data_kantong_bungo_f1(3).pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050003',
            'job_kode' => 'JOB-24050003',
            'designer_kode' => 'USR-24040005',
            'tanggal_pengumpulan' => '2024-05-13',
            'status' => true
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-03',
            'job_assignment_kode' => 'JSM-24050003',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050003',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-03',
            'job_assignment_kode' => 'JSM-24050003',
            'mulai_pengerjaan' => '2024-05-03', 'selesai_pengerjaan' => '2024-05-06',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050003',
            'job_assignment_kode' => 'JSM-24050003',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        TimeLines::create(['event' => 'Lolos QC', 'tanggal_event' => '2024-05-07', 'job_assignment_kode' => 'JSM-24050003', 'quality_control_kode' => 'QCL-24050003']);

        QualityControl::create([
            'kode' => 'QCL-24050004',
            'job_assignment_kode' => 'JSM-24050003',
            'petugas_kode' => 'USR-24040001',
            'status' => 1,
            'komentar' => 'Gambar terong kurang Cerah.'
        ]);

        TimeLines::create(['event' => 'Tidak Lolos Koordinator', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050003', 'quality_control_kode' => 'QCL-24050004']);

        TimeLines::create(['event' => 'Penjadwalan Revisi 1', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050003']);


        //Job 4 || Acc + Reject + Kerja + Acc
        Job::create([
            'kode' => 'JOB-24050004',
            'nama' => 'FARGETIX KAPLET',
            'perusahaan' => 'PT IFARS PHARMACEUTICAL LABORATORIES',
            'tanggal_kirim' => '2024-05-16',
            'status_data' => true,
            'catatan' => 'Dasaran menggunakan Biru Muda.',
            'status' => 0,
            'hasil_design' => 'design_FARGETIX KAPLET240509.png'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050004',
            'nama' => 'data_fargetix_kaplet_(1).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050004',
            'nama' => 'data_fargetix_kaplet_(2).pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050004',
            'job_kode' => 'JOB-24050004',
            'designer_kode' => 'USR-24040005',
            'tanggal_pengumpulan' => '2024-05-10',
            'status' => true
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-04',
            'job_assignment_kode' => 'JSM-24050004',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050004',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-04',
            'job_assignment_kode' => 'JSM-24050004',
            'mulai_pengerjaan' => '2024-05-04', 'selesai_pengerjaan' => '2024-05-06',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050006',
            'job_assignment_kode' => 'JSM-24050004',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        TimeLines::create(['event' => 'Lolos QC', 'tanggal_event' => '2024-05-07', 'job_assignment_kode' => 'JSM-24050004', 'quality_control_kode' => 'QCL-24050006']);

        QualityControl::create([
            'kode' => 'QCL-24050007',
            'job_assignment_kode' => 'JSM-24050004',
            'petugas_kode' => 'USR-24040001',
            'status' => 1,
            'komentar' => 'Text dan Backgroud sedikit Nabrak/Terang Semua.'
        ]);

        TimeLines::create(['event' => 'Tidak Lolos Koordinator', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050004', 'quality_control_kode' => 'QCL-24050007']);

        TimeLines::create(['event' => 'Penjadwalan Revisi 1', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050004']);

        TimeLines::create(['event' => 'Pengumpulan Revisi 1', 'tanggal_event' => '2024-05-09', 'job_assignment_kode' => 'JSM-24050004']);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-08',
            'job_assignment_kode' => 'JSM-24050004',
            'mulai_pengerjaan' => '2024-05-08', 'selesai_pengerjaan' => '2024-05-09',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050008',
            'job_assignment_kode' => 'JSM-24050004',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        TimeLines::create(['event' => 'Lolos QC', 'tanggal_event' => '2024-05-10', 'job_assignment_kode' => 'JSM-24050004', 'quality_control_kode' => 'QCL-24050008']);

        //Job 5 || Data Lengkap
        Job::create([
            'kode' => 'JOB-24050005',
            'nama' => 'YUSIMOX KAPLET',
            'perusahaan' => 'PT IFARS PHARMACEUTICAL LABORATORIES',
            'tanggal_kirim' => '2024-05-25',
            'status_data' => true,
            'catatan' => 'Warna dominan Orange.',
            'status' => 1
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050005',
            'nama' => 'data_yusimox_kaplet.pdf'
        ]);

        //Job 6 || Plot
        Job::create([
            'kode' => 'JOB-24050006',
            'nama' => 'STANDPOUCH BERAS FUKUMI',
            'perusahaan' => 'PT ASIA PRIMA KONJAC',
            'tanggal_kirim' => '2024-05-31',
            'status_data' => true,
            'status' => 0,
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050006',
            'nama' => 'data_standpouch_beras_fukumi(1).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050006',
            'nama' => 'data_standpouch_beras_fukumi(2).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050006',
            'nama' => 'data_standpouch_beras_fukumi(3).pdf'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050006',
            'nama' => 'data_standpouch_beras_fukumi(4).pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050005',
            'job_kode' => 'JOB-24050006',
            'designer_kode' => 'USR-24040006',
            'tanggal_pengumpulan' => '2024-05-15',
            'status' => 1
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-05',
            'job_assignment_kode' => 'JSM-24050005',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-07',
            'job_assignment_kode' => 'JSM-24050005',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-05',
            'job_assignment_kode' => 'JSM-24050005',
            'mulai_pengerjaan' => '2024-05-05', 'selesai_pengerjaan' => '2024-05-07',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050014',
            'job_assignment_kode' => 'JSM-24050005',
            'petugas_kode' => 'USR-24040003',
            'status' => 1,
            'komentar' => 'Komponen belum lengkap.'
        ]);

        TimeLines::create(['event' => 'Tidak Lolos QC', 'tanggal_event' => '2024-05-07', 'job_assignment_kode' => 'JSM-24050005', 'quality_control_kode' => 'QCL-24050014']);

        TimeLines::create(['event' => 'Penjadwalan Revisi 1', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050005',]);

        TimeLines::create(['event' => 'Pengumpulan Revisi 1', 'tanggal_event' => '2024-05-12', 'job_assignment_kode' => 'JSM-24050005',]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-08',
            'job_assignment_kode' => 'JSM-24050005',
            'mulai_pengerjaan' => '2024-05-08', 'selesai_pengerjaan' => '2024-05-12',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050016',
            'job_assignment_kode' => 'JSM-24050005',
            'petugas_kode' => 'USR-24040003',
            'status' => 1,
            'komentar' => 'Komponen Utama terlalu Kecil.'
        ]);

        TimeLines::create(['event' => 'Tidak Lolos QC', 'tanggal_event' => '2024-05-12', 'job_assignment_kode' => 'JSM-24050005', 'quality_control_kode' => 'QCL-24050016']);


        //Job 7 || Data Belum Lengkap
        Job::create([
            'kode' => 'JOB-24050007',
            'nama' => 'PLASTIK KANGKUNG',
            'perusahaan' => 'PT BISI INTERNATIONAL',
            'status_data' => false,
            'status' => 1
        ]);

        //Job 8 || Acc + Acc + Reject Cus + Kerja + Acc + + Acc
        Job::create([
            'kode' => 'JOB-24050008',
            'nama' => 'PLASTIK BUNCIS',
            'Perusahaan' => 'PT BISI INTERNATIONAL',
            'tanggal_kirim' => '2024-05-11',
            'status' => 6,
            'status_data' => true,
            'tanggapan_customer' => 0,
            'hasil_design' => 'design_PLASTIK BUNCIS240507.png'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050008',
            'nama' => 'data_plastik_buncis.pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050006',
            'job_kode' => 'JOB-24050008',
            'designer_kode' => 'USR-24040004',
            'tanggal_pengumpulan' => '2024-05-08',
            'status' => true
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050009',
            'job_assignment_kode' => 'JSM-24050006',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050010',
            'job_assignment_kode' => 'JSM-24050006',
            'petugas_kode' => 'USR-24040001',
            'status' => 0,
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-01',
            'job_assignment_kode' => 'JSM-24050006',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-03',
            'job_assignment_kode' => 'JSM-24050006',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-01',
            'job_assignment_kode' => 'JSM-24050006',
            'mulai_pengerjaan' => '2024-05-01', 'selesai_pengerjaan' => '2024-05-03',
        ]);

        TimeLines::create([
            'event' => 'Lolos QC', 'tanggal_event' => '2024-05-03',
            'quality_control_kode' =>  'QCL-24050009',
            'job_assignment_kode' => 'JSM-24050006',
        ]);

        TimeLines::create([
            'event' => 'Lolos Koordinator', 'tanggal_event' => '2024-05-04',
            'quality_control_kode' =>  'QCL-24050010',
            'job_assignment_kode' => 'JSM-24050006',
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050011',
            'job_assignment_kode' => 'JSM-24050006',
            'petugas_kode' => 'USR-24040002',
            'status' => 1,
            'Komentar' => 'Belum sesuai dengan Komponen Terkait.'
        ]);

        TimeLines::create([
            'event' => 'Pekerjaan ditolak Customer', 'tanggal_event' => '2024-05-05',
            'job_assignment_kode' => 'JSM-24050006', 'quality_control_kode' => 'QCL-24050011'
        ]);

        TimeLines::create([
            'event' => 'Penjadwalan Revisi 1',
            'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050006'
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan Revisi 1',
            'tanggal_event' => '2024-05-07',
            'job_assignment_kode' => 'JSM-24050006'
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-06',
            'job_assignment_kode' => 'JSM-24050006',
            'mulai_pengerjaan' => '2024-05-06', 'selesai_pengerjaan' => '2024-05-07',
        ]);


        QualityControl::create([
            'kode' => 'QCL-24050012',
            'job_assignment_kode' => 'JSM-24050006',
            'petugas_kode' => 'USR-24040003',
            'status' => 0,
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050013',
            'job_assignment_kode' => 'JSM-24050006',
            'petugas_kode' => 'USR-24040001',
            'status' => 0,
        ]);

        TimeLines::create([
            'event' => 'Lolos QC', 'tanggal_event' => '2024-05-08',
            'quality_control_kode' =>  'QCL-24050012',
            'job_assignment_kode' => 'JSM-24050006',
        ]);

        TimeLines::create([
            'event' => 'Lolos Koordinator', 'tanggal_event' => '2024-05-09',
            'quality_control_kode' =>  'QCL-24050013',
            'job_assignment_kode' => 'JSM-24050006',
        ]);


        //Job 9 || Reject + Kerja
        Job::create([
            'kode' => 'JOB-24050009',
            'nama' => 'PLASTIK TIMUN HERCULES',
            'Perusahaan' => 'PT BISI INTERNATIONAL',
            'tanggal_kirim' => '2024-05-27',
            'status' => 4,
            'status_data' => true,
            'catatan' => 'Bagian Judul berwarna Kuning.'
        ]);

        DataPendukung::create([
            'job_kode' => 'JOB-24050009',
            'nama' => 'data_plastik_timun_hercules.pdf'
        ]);

        JobAssignment::create([
            'kode' => 'JSM-24050007',
            'job_kode' => 'JOB-24050009',
            'designer_kode' => 'USR-24040006',
            'tanggal_pengumpulan' => '2024-05-17',
            'status' => true
        ]);

        TimeLines::create([
            'event' => 'Mulai Pengerjaan', 'tanggal_event' => '2024-05-05',
            'job_assignment_kode' => 'JSM-24050007',
        ]);

        TimeLines::create([
            'event' => 'Pengumpulan', 'tanggal_event' => '2024-05-07',
            'job_assignment_kode' => 'JSM-24050007',
        ]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-05',
            'job_assignment_kode' => 'JSM-24050007',
            'mulai_pengerjaan' => '2024-05-05', 'selesai_pengerjaan'=>'2024-05-07'
        ]);

        QualityControl::create([
            'kode' => 'QCL-24050017',
            'job_assignment_kode' => 'JSM-24050007',
            'petugas_kode' => 'USR-24040003',
            'status' => 1,
            'komentar' => 'Warna Judul terlalu Terang. Font tidak terbaca.'
        ]);

        TimeLines::create(['event' => 'Tidak Lolos QC', 'tanggal_event' => '2024-05-08', 'job_assignment_kode' => 'JSM-24050007', 'quality_control_kode' => 'QCL-24050017']);

        TimeLines::create(['event' => 'Penjadwalan Revisi 1', 'tanggal_event' => '2024-05-09', 'job_assignment_kode' => 'JSM-24050007',]);

        TimeLines::create(['event' => 'Pengumpulan Revisi 1', 'tanggal_event' => '2024-05-13', 'job_assignment_kode' => 'JSM-24050007',]);

        TimeLines::create([
            'event' => 'Waktu Pengerjaan', 'tanggal_event' => '2024-05-09',
            'job_assignment_kode' => 'JSM-24050006',
            'mulai_pengerjaan' => '2024-05-09', 'selesai_pengerjaan' => '2024-05-13',
        ]);
    }
}
