<?php

namespace Database\Seeders;

use Database\Seeders\Auth\AccountSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Features\AnnouncementSeeder;
use Database\Seeders\Features\FamilyCardSeeder;
use Database\Seeders\Features\JobTitleSeeder;
use Database\Seeders\Features\PopulationGrowthSeeder;
use Database\Seeders\Gens\BloodTypeSeeder;
use Database\Seeders\Gens\CitizenshipSeeder;
use Database\Seeders\Gens\DocumentTypeSeeder;
use Database\Seeders\Gens\EducationSeeder;
use Database\Seeders\Gens\FacilitiesSeeder;
use Database\Seeders\Gens\JobTypeSeeder;
use Database\Seeders\Gens\MariedStatusSeeder;
use Database\Seeders\Gens\RelationshipStatusSeeder;
use Database\Seeders\Gens\ReligionSeeder;
use Database\Seeders\Static\DocumentStatusSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Statics
            DocumentStatusSeeder::class,

            // Gens
            BloodTypeSeeder::class,
            CitizenshipSeeder::class,
            DocumentTypeSeeder::class,
            EducationSeeder::class,
            JobTypeSeeder::class,
            MariedStatusSeeder::class,
            RelationshipStatusSeeder::class,
            ReligionSeeder::class,
            FacilitiesSeeder::class,
            
            // Auth
            PermissionSeeder::class,
            RoleSeeder::class,
            AccountSeeder::class,
            
            JobTitleSeeder::class,
            FamilyCardSeeder::class,
            PopulationGrowthSeeder::class,
            AnnouncementSeeder::class
        ]);
    }
}
