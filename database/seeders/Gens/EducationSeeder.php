<?php

namespace Database\Seeders\Gens;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educations = [
            'Tidak/Belum Sekolah',
            'SD/Sederajat',
            'SMP/Sederajat',
            'SMA/Sederajat',
            'Diploma I (D1)',
            'Diploma II (D2)',
            'Diploma III (D3)',
            'Strata I (S1)',
            'Strata II (S2)',
            'Strata III (S3)'
        ];

        foreach ($educations as $education) {
            DB::table('educations')->insert([
                'label' => $education,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
