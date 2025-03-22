<?php

namespace Database\Seeders\Features;

use App\Models\Village;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Village::create([
            'history_file' => rand(1, 5),
            'image_file' => rand(1, 5),
            'name' => 'Desa Makmur Jaya',
            'summary' => 'Desa ini didirikan pada tahun 1890 oleh para pendatang dari wilayah sekitarnya.',
            'vision' => 'Menjadi desa mandiri dan sejahtera.',
            'mission' => [
                'Meningkatkan kesejahteraan masyarakat.',
                'Membangun infrastruktur desa yang berkelanjutan.',
                'Meningkatkan pendidikan dan kesehatan masyarakat.',
            ],
            'village_funds' => rand(10000000, 50000000),
        ]);
    }
}
