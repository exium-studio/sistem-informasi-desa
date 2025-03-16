<?php

namespace Database\Seeders\Gens;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTypes = [
            'Belum/Tidak Bekerja',
            'Pelajar/Mahasiswa',
            'Pensiunan',
            'Pegawai Negeri Sipil (PNS)',
            'Tentara Nasional Indonesia (TNI)',
            'Kepolisian RI (POLRI)',
            'Guru/Dosen',
            'Dokter',
            'Bidan/Perawat',
            'Petani/Pekebun',
            'Nelayan',
            'Pedagang',
            'Wiraswasta',
            'Buruh/Karyawan Swasta',
            'Karyawan BUMN',
            'Karyawan BUMD',
            'Karyawan Honorer',
            'Sopir',
            'Seniman',
            'Penyiar Televisi',
            'Penyiar Radio',
            'Pendeta',
            'Pastor',
            'Wartawan',
            'Ustadz/Mubaligh',
            'Juru Masak',
            'Anggota DPR/DPRD',
            'Anggota DPD',
            'Presiden',
            'Wakil Presiden',
            'Bupati',
            'Wakil Bupati',
            'Walikota',
            'Wakil Walikota',
            'Dosen',
            'Peneliti',
            'Sastrawan',
            'Penerjemah',
            'Pendongeng',
            'Desainer',
            'Insinyur',
            'Arsitek',
            'Pustakawan',
            'Teknisi',
            'Tukang Batu',
            'Tukang Kayu',
            'Tukang Las/Pandai Besi',
            'Tukang Jahit',
            'Tukang Listrik',
            'Tukang Ledeng',
            'Mekanik',
            'Seniman',
            'Paranormal',
            'Pialang',
            'Penata Rias',
            'Pengacara',
            'Notaris',
            'Arkeolog',
            'Pilot',
            'Pramugari',
            'Sopir Angkutan Umum',
            'Masinis',
            'Nahkoda',
            'Ojek Online'
        ];

        foreach ($jobTypes as $jobType) {
            DB::table('job_types')->insert([
                'label' => $jobType,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
