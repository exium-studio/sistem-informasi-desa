<?php

namespace Database\Seeders\Features;

use App\Models\Expenses;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeOutcomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $userIds = User::where('id', '!=', 1)->pluck('id')->toArray();
        $year = now()->year;

        $incomeDescriptions = [
            'Dana Bantuan Pemerintah',
            'Sumbangan Warga',
            'Iuran RT',
            'Pemasukan Acara Desa',
            'Penjualan Aset',
            'Donasi Pihak Ketiga',
            'Pendapatan Sewa',
            'Lelang Barang Desa',
        ];

        $expenseDescriptions = [
            'Perbaikan Jalan',
            'Pembelian Alat Kantor',
            'Acara Kemasyarakatan',
            'Biaya Operasional RT/RW',
            'Pembayaran Listrik',
            'Pemeliharaan Sarana Umum',
            'Pengadaan ATK',
            'Transportasi dan Konsumsi Rapat',
        ];

        foreach (range(1, 12) as $month) {
            $numEntries = rand(2, 3); // Jumlah data per bulan

            for ($i = 0; $i < $numEntries; $i++) {
                Income::create([
                    'created_by' => $userIds[array_rand($userIds)],
                    'value' => rand(100_000, 5_000_000),
                    'description' => $incomeDescriptions[array_rand($incomeDescriptions)],
                    'created_at' => Carbon::create($year, $month, rand(1, 28), rand(8, 18)),
                    'updated_at' => now(),
                ]);
            }

            for ($i = 0; $i < $numEntries; $i++) {
                Expenses::create([
                    'created_by' => $userIds[array_rand($userIds)],
                    'value' => rand(50_000, 4_000_000),
                    'description' => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'created_at' => Carbon::create($year, $month, rand(1, 28), rand(8, 18)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
