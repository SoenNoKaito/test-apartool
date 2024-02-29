<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\BuildingOwner;
use App\Models\PropertyManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DisablingUsersWithBuildings extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Reset the database before each test
        $this->artisan('migrate:fresh');

        // Run migrations
        Artisan::call('migrate');

        // Run Passport migrations
        Artisan::call('passport:install');

        // Create a user and authenticate with Passport
        $user = User::factory()->create();
        Passport::actingAs($user);
    }

    public function testDisableBuildingOwner()
    {
        // Create a BuildingOwner with related PropertyManager and Building records
        $buildingOwner = BuildingOwner::factory()->create(['active' => true]);
        $building = Building::factory()->create(['active' => true]);
        $propertyManager = PropertyManager::factory()->create([
            'active' => true,
            'building_id' => $building->id,
            'building_owner_id' => $buildingOwner->id
        ]);

        $response = $this->patchJson("/api/building-owner/{$buildingOwner->id}/disable");
        $response->assertStatus(200);

        // Retrieve the updated records from the database
        $updatedBuildingOwner = BuildingOwner::find($buildingOwner->id);
        $updatedPropertyManager = PropertyManager::where('building_owner_id', $buildingOwner->id)->first();
        $updatedBuilding = Building::where('id', $propertyManager->building_id)->first();

        // Assert that the BuildingOwner and related entities are now inactive
        $this->assertFalse($updatedBuildingOwner->active, 'BuildingOwner is not set to inactive');
        $this->assertFalse($updatedPropertyManager->active, 'PropertyManager is not set to inactive');
        $this->assertFalse($updatedBuilding->active, 'Building is not set to inactive');
    }

}
