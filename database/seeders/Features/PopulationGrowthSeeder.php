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
        // Ambil semua user dengan account_status = 2
        $activeUsers = PopulationGrowth::getUsersWithActiveStatus();

        // Ambil semua user yang register_at sebelum tahun ini
        $registeredBeforeThisYear = PopulationGrowth::getUsersRegisteredBeforeThisYear();

        // Ambil semua user yang deactivate_at sebelum tahun ini
        $deactivatedBeforeThisYear = PopulationGrowth::getUsersDeactivatedBeforeThisYear();

        PopulationGrowth::create([
            'citizen_total' => $activeUsers->count(),
            'new_citizen_total' => $registeredBeforeThisYear->count(),
            'leave_citizen_total' => $deactivatedBeforeThisYear->count(),
            'year' => Carbon::now()->year,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
