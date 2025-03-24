<?php

namespace Database\Seeders\Auth;

use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('name')->toArray();

        $super_admin_account = User::create([
            'name' => 'Super Admin',
            'username' => 'super.admin',
            'email' => 'distrostudiodev@gmail.com',
            'account_status' => 2,
            'password' => Hash::make('superadmin123'),
            'register_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $super_admin_account->assignRole('Super Admin');

        foreach ($roles as $role) {
            if ($role !== 'Super Admin') {
                for ($i = 1; $i <= 50; $i++) {
                    $facilities = DB::table('facilities')->pluck('id')->toArray();
                    $document_types = DB::table('document_types')->pluck('id')->toArray();

                    // Generate random filters based on the number of IDs in the respective tables
                    $facilities_filter = $this->getRandomFromArray($facilities);
                    $document_type_filter = $this->getRandomFromArray($document_types);
                    $registerDate = Carbon::create(2024, rand(1, 12), rand(1, 28));

                    $user = User::create([
                        'name' => $role . ' ' . $i,
                        'username' => strtolower(str_replace(' ', '', $role)) . $i,
                        'email' => strtolower(str_replace(' ', '', $role)) . $i . '@example.com',
                        'account_status' => 2,
                        'password' => Hash::make('password123'),
                        'facilities_filter' => $facilities_filter,
                        'document_type_filter' => $document_type_filter,
                        'register_at' => $registerDate,
                        'created_at' => $registerDate,
                        'updated_at' => $registerDate
                    ]);

                    $user->assignRole($role);

                    $religion_ids = DB::table('religions')->pluck('id')->toArray();
                    $education_ids = DB::table('educations')->pluck('id')->toArray();
                    $job_type_ids = DB::table('job_types')->pluck('id')->toArray();
                    $blood_type_ids = DB::table('blood_types')->pluck('id')->toArray();
                    $maried_status_ids = DB::table('maried_statuses')->pluck('id')->toArray();
                    $relationship_status_ids = DB::table('relationship_statuses')->pluck('id')->toArray();
                    $citizenship_ids = DB::table('citizenships')->pluck('id')->toArray();

                    // Create Resident for the user
                    Resident::create([
                        'user_id' => $user->id,
                        'religion_id' => $this->getRandomFromArray($religion_ids)[0],
                        'education_id' => $this->getRandomFromArray($education_ids)[0],
                        'job_type_id' => $this->getRandomFromArray($job_type_ids)[0],
                        'blood_type_id' => $this->getRandomFromArray($blood_type_ids)[0],
                        'maried_status_id' => $this->getRandomFromArray($maried_status_ids)[0],
                        'relationship_status_id' => $this->getRandomFromArray($relationship_status_ids)[0],
                        'citizenship_id' => $this->getRandomFromArray($citizenship_ids)[0],
                        'gender' => rand(0, 1), // 1 = male, 0 = female
                        'place_of_birth' => 'Place ' . $i,
                        'date_of_birth' => Carbon::now()->subYears(rand(20, 50))->format('Y-m-d'), // Random age between 20 and 50
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
    }

    private function getRandomFromArray(array $ids): array
    {
        // Choose a random number of elements from the array between 3 and 5 for filters (facilities & document types)
        $randomCount = rand(3, 5);
        shuffle($ids);
        return array_slice($ids, 0, $randomCount);
    }
}
