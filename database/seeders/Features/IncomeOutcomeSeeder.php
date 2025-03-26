<?php

namespace Database\Seeders\Features;

use App\Models\ExpenseCategory;
use App\Models\Expenses;
use App\Models\Income;
use App\Models\IncomeSource;
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
        $incomeSourceIds = IncomeSource::pluck('id')->toArray();
        $expenseCategoryIds = ExpenseCategory::pluck('id')->toArray();

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
                $date = Carbon::create($year, $month, rand(1, 28), rand(8, 18));

                Income::create([
                    'created_by' => $userIds[array_rand($userIds)],
                    'income_source_id' => $incomeSourceIds[array_rand($incomeSourceIds)],
                    'value' => rand(100_000, 5_000_000),
                    'description' => $incomeDescriptions[array_rand($incomeDescriptions)],
                    'realization_date' => $date,
                    'created_at' => $date,
                    'updated_at' => now(),
                ]);
            }

            for ($i = 0; $i < $numEntries; $i++) {
                $date = Carbon::create($year, $month, rand(1, 28), rand(8, 18));

                Expenses::create([
                    'created_by' => $userIds[array_rand($userIds)],
                    'expense_category_id' => $expenseCategoryIds[array_rand($expenseCategoryIds)],
                    'value' => rand(50_000, 4_000_000),
                    'description' => $expenseDescriptions[array_rand($expenseDescriptions)],
                    'realization_date' => $date,
                    'created_at' => $date,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
