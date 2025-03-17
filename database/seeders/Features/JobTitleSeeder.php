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
                'description' => 'Pemimpin pemerintahan desa yang bertanggung jawab atas pengelolaan administrasi dan pembangunan desa.',
                'facilities_filter' => json_encode([1, 2, 3]),
                'civil_doc_type_filter' => json_encode([1, 2, 3]),
            ],
            [
                'name' => 'Sekretaris Desa',
                'description' => 'Membantu Kepala Desa dalam administrasi dan pelayanan masyarakat di desa.',
                'facilities_filter' => json_encode([1, 3]),
                'civil_doc_type_filter' => json_encode([1, 4, 5]),
            ],
            [
                'name' => 'Kepala Dusun',
                'description' => 'Mengurus administrasi dan keamanan di tingkat dusun dalam desa.',
                'facilities_filter' => json_encode([4, 5]),
                'civil_doc_type_filter' => json_encode([6, 7]),
            ],
            [
                'name' => 'Ketua RT',
                'description' => 'Bertanggung jawab atas koordinasi kegiatan dan administrasi di tingkat Rukun Tetangga.',
                'facilities_filter' => json_encode([6, 7]),
                'civil_doc_type_filter' => json_encode([2, 8]),
            ],
            [
                'name' => 'Ketua RW',
                'description' => 'Mengkoordinasikan berbagai program masyarakat di tingkat Rukun Warga.',
                'facilities_filter' => json_encode([1, 8]),
                'civil_doc_type_filter' => json_encode([1, 9]),
            ],
        ];

        foreach ($jobTitles as $jobTitle) {
            DB::table('job_titles')->insert([
                'name' => $jobTitle['name'],
                'description' => $jobTitle['description'],
                'facilities_filter' => $jobTitle['facilities_filter'],
                'civil_doc_type_filter' => $jobTitle['civil_doc_type_filter'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
