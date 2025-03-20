<?php

namespace Database\Seeders\Features;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTitles = [
            [
                'name' => 'Kepala Desa',
                'description' => 'Pemimpin pemerintahan desa yang bertanggung jawab atas pengelolaan administrasi dan pembangunan desa.'
            ],
            [
                'name' => 'Sekretaris Desa',
                'description' => 'Membantu Kepala Desa dalam administrasi dan pelayanan masyarakat di desa.'
            ],
            [
                'name' => 'Kepala Dusun',
                'description' => 'Mengurus administrasi dan keamanan di tingkat dusun dalam desa.'
            ],
            [
                'name' => 'Ketua RT',
                'description' => 'Bertanggung jawab atas koordinasi kegiatan dan administrasi di tingkat Rukun Tetangga.'
            ],
            [
                'name' => 'Ketua RW',
                'description' => 'Mengkoordinasikan berbagai program masyarakat di tingkat Rukun Warga.'
            ],
        ];

        foreach ($jobTitles as $jobTitle) {
            DB::table('job_titles')->insert([
                'name' => $jobTitle['name'],
                'description' => $jobTitle['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
