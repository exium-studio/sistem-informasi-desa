<?php

namespace Database\Seeders\Features;

use App\Models\PopulationGrowth;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PopulationGrowthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = now()->year - 4; // 5 tahun ke belakang termasuk tahun ini
        $previousTotal = 40; // nilai awal penduduk (dummy)

        foreach (range($startYear, now()->year) as $year) {
            $newCitizens = rand(50, 150);  // warga baru
            $leaveCitizens = rand(20, 100); // warga pindah/meninggal

            // Hitung total penduduk tahun ini
            $currentTotal = max(0, $previousTotal + $newCitizens - $leaveCitizens);

            PopulationGrowth::create([
                'citizen_total' => $currentTotal,
                'new_citizen_total' => $newCitizens,
                'leave_citizen_total' => $leaveCitizens,
                'year' => $year,
                'created_at' => Carbon::create($year, 12, 31),
                'updated_at' => Carbon::create($year, 12, 31),
            ]);

            $previousTotal = $currentTotal;
        }
    }
}
