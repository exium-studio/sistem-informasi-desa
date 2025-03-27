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
            // Dashboard section
            ['name' => 'population.view', 'description' => 'View Population', 'group' => 'population'],
            ['name' => 'village.view', 'description' => 'View Village', 'group' => 'village'],
            ['name' => 'village.edit', 'description' => 'Edit Village', 'group' => 'village'],
            ['name' => 'announcement.view', 'description' => 'View Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.create', 'description' => 'Create Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.edit', 'description' => 'Edit Announcement', 'group' => 'announcement'],
            ['name' => 'announcement.delete', 'description' => 'Delete Announcement', 'group' => 'announcement'],
            ['name' => 'officialcontact.view', 'description' => 'View Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.create', 'description' => 'Create Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.edit', 'description' => 'Edit Official Contact', 'group' => 'official_contact'],
            ['name' => 'officialcontact.delete', 'description' => 'Delete Official Contact', 'group' => 'official_contact'],
            ['name' => 'facility.view', 'description' => 'View Facility', 'group' => 'facility'],
            ['name' => 'facility.create', 'description' => 'Create Facility', 'group' => 'facility'],
            ['name' => 'facility.edit', 'description' => 'Edit Facility', 'group' => 'facility'],
            ['name' => 'facility.delete', 'description' => 'Delete Facility', 'group' => 'facility'],
            ['name' => 'fundmutation.view', 'description' => 'View Fund Mutation', 'group' => 'fund_mutation'],
            ['name' => 'fundmutation.create', 'description' => 'Create Fund Mutation', 'group' => 'fund_mutation'],
            ['name' => 'fundmutation.edit', 'description' => 'Edit Fund Mutation', 'group' => 'fund_mutation'],
            ['name' => 'fundmutation.delete', 'description' => 'Delete Fund Mutation', 'group' => 'fund_mutation'],

            // Header section
            ['name' => 'inbox.view', 'description' => 'View Inbox', 'group' => 'inbox'],
            ['name' => 'inbox.create', 'description' => 'Create Inbox', 'group' => 'inbox'],
            ['name' => 'inbox.edit', 'description' => 'Edit Inbox', 'group' => 'inbox'],
            ['name' => 'inbox.delete', 'description' => 'Delete Inbox', 'group' => 'inbox'],
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
