<?php

namespace Database\Seeders\Features;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilyCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->where('id', '!=', 1)->pluck('id')->unique()->toArray();
        shuffle($users); // Acak urutan agar unik
        $totalUsers = count($users);
        $numRecords = min($totalUsers, 10);
        $selectedUsers = array_slice($users, 0, $numRecords);

        $familyCards = [];

        foreach ($selectedUsers as $userId) {
            $familyCards[] = [
                'user_id' => $userId,
                'no_kk' => str_pad(mt_rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT),
                'rt' => str_pad(mt_rand(1, 10), 3, '0', STR_PAD_LEFT),
                'rw' => str_pad(mt_rand(1, 10), 3, '0', STR_PAD_LEFT),
                'village' => json_encode(['id' => mt_rand(100, 199), 'name' => 'Kelurahan ' . chr(mt_rand(65, 90))]),
                'sub_district' => json_encode(['id' => mt_rand(200, 299), 'name' => 'Kecamatan ' . chr(mt_rand(65, 90))]),
                'city_regency' => json_encode(['id' => mt_rand(300, 399), 'name' => 'Kota/Kabupaten ' . chr(mt_rand(65, 90))]),
                'province' => json_encode(['id' => mt_rand(400, 499), 'name' => 'Provinsi ' . chr(mt_rand(65, 90))]),
                'postal_code' => str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('family_cards')->insert($familyCards);
    }
}
