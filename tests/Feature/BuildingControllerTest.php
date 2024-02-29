<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\BuildingOwner;
use App\Models\PropertyManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuildingControllerTest extends TestCase
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

    public function testGetAllBuildingsWithoutFilters()
    {
        // Create a few building records with associated property managers and building owners
        $buildingOwners = BuildingOwner::factory()->count(3)->create();
        $buildings = Building::factory()->count(3)->create()->each(function ($building) use ($buildingOwners) {
            PropertyManager::factory()->create([
                'building_id' => $building->id,
                'building_owner_id' => $buildingOwners->random()->id,
            ]);
        });

        $response = $this->getJson('/api/buildings/list');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name', 'code', 'address', 'active', 'property_managers' => [
                        '*' => ['first_name', 'last_name', 'email']
                    ]
                ]
            ]
        ]);

        // Assert that the number of buildings returned matches the number created
        $response->assertJsonCount(3, 'data');
    }

    public function testGetBuildingsWithNameFilter()
    {
        $owner = BuildingOwner::factory()->create();
        $building = Building::factory()->create(['name' => 'Sunset Towers']);
        PropertyManager::factory()->create([
            'building_id' => $building->id,
            'building_owner_id' => $owner->id
        ]);

        $response = $this->getJson('/api/buildings/list?name=Sunset');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Sunset Towers']);
        $response->assertJsonCount(1, 'data');
    }

    public function testGetActiveBuildings()
    {
        Building::factory()->create(['active' => true]);
        Building::factory()->create(['active' => false]);

        $response = $this->getJson('/api/buildings/list?active=1');
        $response->assertStatus(200);
        $response->assertJsonFragment(['active' => 1]);
    }

    public function testGetInactiveBuildings()
    {
        Building::factory()->create(['active' => true]);
        Building::factory()->create(['active' => false]);

        $response = $this->getJson('/api/buildings/list?active=0');
        $response->assertStatus(200);
        $response->assertJsonFragment(['active' => 0]);
    }

    public function testGetActiveAndInactiveBuildings()
    {
        // Create 2 active and 1 inactive building
        Building::factory()->count(2)->create(['active' => true]);
        Building::factory()->create(['active' => false]);

        $response = $this->getJson('/api/buildings/list'); // No 'active' filter applied
        $response->assertStatus(200);

        // Assert that the response contains 3 buildings in total
        $response->assertJsonCount(3, 'data');
    }

    public function testGetBuildingsByOwnerNameFilter()
    {
        $owner = BuildingOwner::factory()->create(['first_name' => 'John']);
        $building = Building::factory()->create();
        PropertyManager::factory()->create([
            'building_id' => $building->id,
            'building_owner_id' => $owner->id
        ]);

        $response = $this->getJson('/api/buildings/list?building_owner_name=John');
        $response->assertStatus(200);

        // Extract the property managers from the response
        $propertyManagers = collect($response->json('data.*.property_managers'))->collapse();

        // Check if any property manager has the first name 'John'
        $this->assertTrue($propertyManagers->contains('first_name', 'John'));
    }

    public function testGetBuildingsByOwnerLastNameFilter()
    {
        $owner = BuildingOwner::factory()->create(['last_name' => 'Smith']);
        $building = Building::factory()->create();
        PropertyManager::factory()->create([
            'building_id' => $building->id,
            'building_owner_id' => $owner->id
        ]);

        $response = $this->getJson('/api/buildings/list?building_owner_last_name=Smith');
        $response->assertStatus(200);

        // Assert that the response contains buildings with property managers linked to an owner with last name 'Smith'
        $buildingsData = $response->json('data');
        $found = false;
        foreach ($buildingsData as $buildingData) {
            foreach ($buildingData['property_managers'] as $manager) {
                if ($manager['last_name'] === 'Smith') {
                    $found = true;
                    break 2; // Break out of both loops
                }
            }
        }
        $this->assertTrue($found, 'No property managers with last name Smith found in response');
    }

    public function testGetBuildingsByActiveAndNameAndOwnerLastNameFilter()
    {
        $owner = BuildingOwner::factory()->create(['last_name' => 'Johnson']);
        $activeBuilding = Building::factory()->create(['name' => 'Sunrise Apartments', 'active' => true]);
        PropertyManager::factory()->create([
            'building_id' => $activeBuilding->id,
            'building_owner_id' => $owner->id
        ]);

        // Create additional buildings to ensure the filter is working
        Building::factory()->create(['name' => 'Sunrise Apartments', 'active' => false]);
        Building::factory()->create(['name' => 'Other Building', 'active' => true]);

        $response = $this->getJson('/api/buildings/list?name=Sunrise&active=1&building_owner_last_name=Johnson');
        $response->assertStatus(200);

        // Assert that the response contains only the building that matches all criteria
        $buildingsData = $response->json('data');
        $this->assertCount(1, $buildingsData);
        $this->assertEquals('Sunrise Apartments', $buildingsData[0]['name']);
        $this->assertEquals(1, $buildingsData[0]['active']);
        $this->assertEquals('Johnson', $buildingsData[0]['property_managers'][0]['last_name']);
    }


}
