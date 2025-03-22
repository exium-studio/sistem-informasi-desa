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
            ['name' => 'officialcontact.view', 'description' => 'View Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.create', 'description' => 'Create Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.edit', 'description' => 'Edit Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.delete', 'description' => 'Delete Official Contact', 'group' => 'official_contact'],
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
