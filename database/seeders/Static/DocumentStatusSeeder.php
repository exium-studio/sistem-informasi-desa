<?php

namespace Database\Seeders\Static;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docs_statuses = [
            'Menunggu',
            'Diverifikasi',
            'Ditolak'
        ];

        foreach ($docs_statuses as $docs) {
            DB::table('document_statuses')->insert([
                'label' => $docs,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
