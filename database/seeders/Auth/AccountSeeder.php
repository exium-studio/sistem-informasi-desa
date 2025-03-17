<?php

namespace Database\Seeders\Auth;

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
                for ($i = 1; $i <= 10; $i++) {
                    $user = User::create([
                        'name' => $role . ' ' . $i,
                        'username' => strtolower(str_replace(' ', '', $role)) . $i,
                        'email' => strtolower(str_replace(' ', '', $role)) . $i . '@example.com',
                        'account_status' => 2,
                        'password' => Hash::make('password123'),
                        'register_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    $user->assignRole($role);
                }
            }
        }
    }
}
