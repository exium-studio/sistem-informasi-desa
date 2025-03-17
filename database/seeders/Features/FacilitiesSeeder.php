<?php

namespace Database\Seeders\Features;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['name' => 'Masjid', 'description' => 'Tempat ibadah umat Islam untuk melaksanakan salat dan kegiatan keagamaan lainnya.'],
            ['name' => 'Lapangan', 'description' => 'Area terbuka yang digunakan untuk olahraga, upacara, dan berbagai kegiatan masyarakat.'],
            ['name' => 'Gor Desa', 'description' => 'Gedung olahraga desa yang digunakan untuk berbagai aktivitas olahraga dan acara masyarakat.'],
            ['name' => 'Gedung Serbaguna', 'description' => 'Fasilitas yang dapat digunakan untuk berbagai acara sosial, budaya, dan pemerintahan desa.'],
            ['name' => 'Tempat Ibadah', 'description' => 'Sarana ibadah yang tersedia untuk berbagai agama sesuai dengan kebutuhan masyarakat.'],
            ['name' => 'Taman/Ruang Terbuka Hijau (RTH)', 'description' => 'Area hijau yang berfungsi sebagai ruang rekreasi, olahraga, dan penghijauan lingkungan.'],
            ['name' => 'Gedung Karang Taruna', 'description' => 'Fasilitas yang digunakan oleh organisasi kepemudaan untuk berbagai kegiatan sosial dan pemberdayaan.'],
            ['name' => 'Pasar', 'description' => 'Tempat transaksi jual beli berbagai kebutuhan masyarakat, baik pangan maupun sandang.'],
            ['name' => 'Puskesmas', 'description' => 'Fasilitas kesehatan utama yang memberikan layanan medis dasar bagi masyarakat.'],
            ['name' => 'Posyandu', 'description' => 'Pos pelayanan terpadu yang memberikan layanan kesehatan ibu dan anak serta edukasi gizi masyarakat.']
        ];

        foreach ($facilities as $facility) {
            DB::table('facilities')->insert([
                'name' => $facility['name'],
                'description' => $facility['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
