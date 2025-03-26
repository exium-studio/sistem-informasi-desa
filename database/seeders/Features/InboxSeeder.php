<?php

namespace Database\Seeders\Features;

use App\Models\Inbox;
use App\Models\InboxType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::where('id', '!=', 1)->pluck('id')->toArray();
        $inboxTypeIds = InboxType::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            $createdBy = $userIds[array_rand($userIds)];

            // Ambil 2–5 user ID secara acak untuk received_by
            shuffle($userIds);
            $receivedBy = array_slice($userIds, 0, rand(2, 5));

            Inbox::create([
                'created_by' => $createdBy,
                'received_by' => $receivedBy,
                'inbox_type_id' => $inboxTypeIds[array_rand($inboxTypeIds)],
                'message' => 'Pesan penting: ' . Str::random(30),
                'is_verified' => (bool) rand(0, 1),
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
