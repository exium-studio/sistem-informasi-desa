<?php

namespace Database\Seeders\Features;

use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '!=', 1)->pluck('id')->toArray();
        $statuses = DocumentStatus::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            $statusId = $statuses[array_rand($statuses)];
            Document::create([
                'document_status_id' => $statusId,
                'verified_by' => rand(0, 1) ? $users[array_rand($users)] : null,
                'uploaded_by' => rand(0, 1) ? $users[array_rand($users)] : null,
                'file_id' => Str::uuid(),
                'file_name' => 'document_' . ($i + 1) . '.pdf',
                'file_path' => 'uploads/documents/document_' . ($i + 1) . '.pdf',
                'file_mime_type' => 'application/pdf',
                'file_size' => rand(100, 500) . ' KB',
                'reason' => $statusId == 3 ? 'Approval required' : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
