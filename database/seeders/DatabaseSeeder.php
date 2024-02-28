<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\BuildingOwner;
use App\Models\PropertyManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a user with the specified name, email, and password
        User::factory()->create();

        //Create data for the BuildingOwner, Building, and PropertyManager models
        BuildingOwner::factory(50)->create();
        Building::factory(50)->create();

        $buildingOwners = BuildingOwner::all();
        $buildings = Building::all();


        // Create a random number of PropertyManager records for each BuildingOwner
        $buildingOwners->each(function ($buildingOwner) use ($buildings) {
            $numberOfBuildings = rand(0, 3);

            $randomBuildings = $buildings->random($numberOfBuildings);

            $randomBuildings->each(function ($randomBuilding) use ($buildingOwner) {
                PropertyManager::factory()->create([
                    'building_owner_id' => $buildingOwner->id,
                    'building_id' => $randomBuilding->id,
                ]);
            });
        });
    }
}
