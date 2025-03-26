<?php

namespace Database\Seeders\Gens;

use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Infrastruktur (Jalan, Jembatan, Drainase)',
            'Pendidikan dan Pelatihan',
            'Kesehatan dan Posyandu',
            'Bantuan Sosial',
            'Operasional Pemerintahan Desa',
            'Pembangunan dan Rehabilitasi Fasilitas Umum',
            'Pengembangan Ekonomi Desa (BUMDes)',
            'Kebersihan dan Lingkungan Hidup',
            'Keamanan dan Ketertiban (Linmas, Satgas)',
            'Lain-lain Pengeluaran Desa',
        ];

        foreach ($categories as $label) {
            ExpenseCategory::create([
                'label' => $label,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
