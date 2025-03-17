<?php

namespace Database\Seeders\Auth;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $created_at = Carbon::now()->subDays(rand(1, 30));
        $updated_at = Carbon::now()->subDays(rand(1, 30));

        $SuperAdmin = Role::create([
            'name' => 'Super Admin',
            'description' => 'Ini adalah role Super Admin',
            'guard_name' => 'web',
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);

        $KepalaDesa = Role::create([
            'name' => 'Lurah',
            'description' => 'Ini adalah role Lurah',
            'guard_name' => 'web',
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);

        $RW = Role::create([
            'name' => 'Kepala RW',
            'description' => 'Ini adalah role RW',
            'guard_name' => 'web',
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);

        $RT = Role::create([
            'name' => 'Kepala RT',
            'description' => 'Ini adalah role RT',
            'guard_name' => 'web',
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);

        $WargaSipil = Role::create([
            'name' => 'Warga Sipil',
            'description' => 'Ini adalah role Warga Sipil',
            'guard_name' => 'web',
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ]);

        $SuperAdmin->givePermissionTo(Permission::all());
    }
}
