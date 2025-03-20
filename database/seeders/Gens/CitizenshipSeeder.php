<?php

namespace Database\Seeders\Gens;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitizenshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citizenships = [
            'Indonesia',
            'Singapura',
            'Malaysia',
            'Thailand',
            'Vietnam',
            'Myanmar',
            'Kamboja',
            'Laos',
            'Brunei',
            'Filipina',
            'Timor Leste',
            'Australia',
            'United States',
            'Canada',
            'United Kingdom',
            'Germany',
            'France',
            'Italy',
            'Japan',
            'South Korea',
        ];

        foreach ($citizenships as $citizenship) {
            DB::table('citizenships')->insert([
                'label' => $citizenship,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
