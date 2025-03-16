<?php

namespace Database\Seeders\Gens;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MariedStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marriedStatuses = [
            'Belum Kawin',
            'Kawin',
            'Cerai Hidup',
            'Cerai Mati'
        ];

        foreach ($marriedStatuses as $status) {
            DB::table('maried_statuses')->insert([
                'label' => $status,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
