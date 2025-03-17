<?php

namespace Database\Seeders;

use Database\Seeders\Auth\AccountSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Features\FacilitiesSeeder;
use Database\Seeders\Features\FamilyCardSeeder;
use Database\Seeders\Features\JobTitleSeeder;
use Database\Seeders\Gens\BloodTypeSeeder;
use Database\Seeders\Gens\CitizenshipSeeder;
use Database\Seeders\Gens\CivilDoctypeSeeder;
use Database\Seeders\Gens\EducationSeeder;
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
            // Auth
            PermissionSeeder::class,
            RoleSeeder::class,
            AccountSeeder::class,

            // Statics
            DocumentStatusSeeder::class,

            // Gens
            BloodTypeSeeder::class,
            CitizenshipSeeder::class,
            CivilDoctypeSeeder::class,
            EducationSeeder::class,
            JobTypeSeeder::class,
            MariedStatusSeeder::class,
            RelationshipStatusSeeder::class,
            ReligionSeeder::class,
            
            // Features
            FacilitiesSeeder::class,
            JobTitleSeeder::class,
            FamilyCardSeeder::class
        ]);
    }
}
