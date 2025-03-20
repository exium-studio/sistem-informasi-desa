<?php

namespace Database\Seeders\Features;

use App\Models\Announcement;
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

        // Buat 5 pengumuman
        foreach (range(1, 5) as $index) {
            Announcement::create([
                'created_by' => $users->random()->id,
                'title' => 'Announcement ' . $index,
                'description' => 'This is the description for announcement ' . $index,
                'location' => [
                    'lat' => rand(-90, 90),
                    'long' => rand(-180, 180)
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
