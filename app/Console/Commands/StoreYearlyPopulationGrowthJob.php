<?php

namespace App\Console\Commands;

use App\Models\PopulationGrowth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StoreYearlyPopulationGrowthJob extends Command
{
    protected $signature = 'population:store-growth';
    protected $description = 'Menyimpan data pertumbuhan populasi untuk tahun sebelumnya';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = now()->subYear()->year;
        $start = Carbon::create($year)->startOfYear();
        $end = Carbon::create($year)->endOfYear();

        $citizen_total = User::where('id', '!=', 1)
            ->where('account_status', 2)
            ->count();

        $new_citizen_total = User::where('id', '!=', 1)
            ->whereBetween('register_at', [$start, $end])
            ->count();

        $leave_citizen_total = User::where('id', '!=', 1)
            ->whereBetween('deactivate_at', [$start, $end])
            ->count();

        PopulationGrowth::updateOrCreate(
            ['year' => $year],
            [
                'citizen_total' => $citizen_total,
                'new_citizen_total' => $new_citizen_total,
                'leave_citizen_total' => $leave_citizen_total,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->info("Data pertumbuhan populasi untuk tahun {$year} berhasil disimpan.");
        return Command::SUCCESS;
    }
}
