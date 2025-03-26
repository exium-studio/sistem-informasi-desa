<?php

namespace Database\Seeders\Gens;

use App\Models\Document;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = Document::pluck('id')->toArray();

        foreach (range(1, 10) as $i) {
            $amount = rand(5, 50);
            $amountUsage = rand(0, $amount);

            Inventory::create([
                'name' => 'Inventaris ' . $i,
                'description' => 'Keterangan inventaris: ' . Str::random(30),
                'amount' => $amount,
                'amount_usage' => $amountUsage,
                'image_file' => $this->generateRandomDocumentIds($documents),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Fungsi untuk menghasilkan array document_id secara acak
     */
    private function generateRandomDocumentIds(array $documentIds): array
    {
        if (empty($documentIds)) return [];

        shuffle($documentIds);
        $randomCount = rand(1, min(3, count($documentIds)));
        return array_slice($documentIds, 0, $randomCount);
    }
}
