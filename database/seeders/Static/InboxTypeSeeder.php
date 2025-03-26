<?php

namespace Database\Seeders\Static;

use App\Models\InboxType;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InboxTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Administrasi Kependudukan',
            'Administrasi Peristiwa Penting (Sipil)',
            'Administrasi Pertanahan',
            'Administrasi Surat Aproval',
            'Persetujuan Bangunan Gedung',
            'Pemakaian Fasilitas',
            'Pemakaian Inventaris',
            'Layanan Darurat',
            'Laporan Aduan Warga'
        ];

        foreach ($types as $label) {
            InboxType::create([
                'label' => $label,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
