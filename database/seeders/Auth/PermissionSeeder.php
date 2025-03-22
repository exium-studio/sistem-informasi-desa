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
            ['name' => 'announcement.view', 'description' => 'View Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.create', 'description' => 'Create Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.edit', 'description' => 'Edit Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.delete', 'description' => 'Delete Announcement', 'group' => 'announcement'],
            ['name' => 'officialcontact.view', 'description' => 'View Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.create', 'description' => 'Create Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.edit', 'description' => 'Edit Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.delete', 'description' => 'Delete Official Contact', 'group' => 'official_contact'],
            ['name' => 'village.view', 'description' => 'View Village', 'group' => 'village'],
            ['name' => 'village.edit', 'description' => 'Edit Village', 'group' => 'village'],
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
