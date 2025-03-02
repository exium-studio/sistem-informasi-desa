<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $super_admin_account = User::create([
            'name' => 'Super Admin',
            'username' => 'super.admin',
            'email' => 'distrostudiodev@gmail.com',
            'account_status' => 2,
            'password' => Hash::make('superadmin123'),
            'register_at' => Carbon::now('Asia/Jakarta')
        ]);
        
        $super_admin_account->assignRole('Super Admin');
    }
}
