<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view', 'description' => 'View Dashboard', 'group' => 'dashboard'],
            ['name' => 'pengumuman.view', 'description' => 'View Pengumuman', 'group' => 'pengumuman'],
            ['name' => 'pengumuman.create', 'description' => 'Create Pengumuman', 'group' => 'pengumuman'],
            ['name' => 'pengumuman.edit', 'description' => 'Edit Pengumuman', 'group' => 'pengumuman'],
            ['name' => 'pengumuman.delete', 'description' => 'Delete Pengumuman', 'group' => 'pengumuman'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web',
            ], [
                'description' => $permission['description'],
                'group' => $permission['group'],
            ]);
        }
    }
}
