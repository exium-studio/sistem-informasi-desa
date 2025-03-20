<?php

namespace Database\Seeders\Features;

use App\Models\Expenses;
use App\Models\Income;
use App\Models\User;
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

        // incomes
        for ($i = 0; $i < 10; $i++) {
            Income::create([
                'created_by' => $userIds[array_rand($userIds)],
                'value' => rand(10000, 500000),
                'description' => 'Pemasukan: ' . Str::random(20),
            ]);
        }

        // expenses
        for ($i = 0; $i < 10; $i++) {
            Expenses::create([
                'created_by' => $userIds[array_rand($userIds)],
                'value' => rand(5000, 400000),
                'description' => 'Pengeluaran: ' . Str::random(20),
            ]);
        }
    }
}
