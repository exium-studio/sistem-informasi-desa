<?php

namespace Database\Seeders\Features;

use App\Models\Announcement;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->get();
        $documentIds = Document::pluck('id')->toArray();

        // Buat 5 pengumuman
        foreach (range(1, 5) as $index) {
            $publishedAt = Carbon::now()->subDays(rand(1, 30)); // Tanggal publikasi acak dalam 30 hari terakhir
            $expiresAt = (clone $publishedAt)->addDays(rand(1, 10)); // Expiry antara 1-10 hari setelah publikasi

            Announcement::create([
                'created_by' => $users->random()->id,
                'document_id' => $this->generateRandomDocumentIds($documentIds),
                'title' => 'Announcement ' . $index,
                'description' => 'This is the description for announcement ' . $index,
                'location' => [
                    'lat' => rand(-90, 90),
                    'long' => rand(-180, 180)
                ],
                'published_at' => $publishedAt,
                'expires_at' => $expiresAt,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Fungsi untuk menghasilkan array document_id secara acak
     */
    private function generateRandomDocumentIds($documentIds)
    {
        shuffle($documentIds);
        $randomCount = rand(1, 3);
        return array_slice($documentIds, 0, $randomCount);
    }
}
