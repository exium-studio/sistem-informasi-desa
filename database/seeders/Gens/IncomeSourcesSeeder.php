<?php

namespace Database\Seeders\Gens;

use App\Models\IncomeSource;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Dana Desa',
            'Alokasi Dana Desa (ADD)',
            'Pajak dan Retribusi Daerah',
            'Hibah Pemerintah',
            'Hibah Swasta atau Lembaga',
            'Pendapatan dari BUMDes',
            'Bantuan Sosial',
            'Sumbangan Masyarakat',
            'Hasil Kekayaan Desa (Tanah Kas Desa, Aset Desa)',
            'Lain-lain Pendapatan Sah',
        ];

        foreach ($sources as $label) {
            IncomeSource::create([
                'label' => $label,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
