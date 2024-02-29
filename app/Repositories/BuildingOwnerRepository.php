<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\BuildingOwner;
use App\Models\PropertyManager;

class BuildingOwnerRepository
{
    public function disableBuildingOwner($id): void
    {
        $buildingOwner = BuildingOwner::findOrFail($id);
        $buildingOwner->active = false;
        $buildingOwner->save();

        // Disable related property managers and buildings
        PropertyManager::where('building_owner_id', $buildingOwner->id)
            ->each(function ($manager) {
                $manager->active = false;
                $manager->save();

                Building::where('id', $manager->building_id)->update(['active' => false]);
            });
    }
}
