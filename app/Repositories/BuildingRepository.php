<?php

namespace App\Repositories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Builder;

class BuildingRepository
{
    public function getAllWithFilters(array $filters, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Building::query();

        $this->applyFilters($query, $filters);

        $query->with([
            'propertyManagers' => function ($query) {
                $query->with(['buildingOwner:id,first_name,last_name,email,active'])
                    ->select('id', 'building_owner_id', 'building_id', 'active');
            },
        ])->select('id', 'name', 'active', 'code', 'address');

        return $query->paginate($perPage);
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        // Apply 'active' filter if set, otherwise include all buildings
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['building_owner_name']) || isset($filters['building_owner_last_name'])) {
            $query->whereHas('propertyManagers.buildingOwner', function (Builder $query) use ($filters) {
                if (isset($filters['building_owner_name'])) {
                    $query->where('first_name', 'LIKE', "%{$filters['building_owner_name']}%");
                }
                if (isset($filters['building_owner_last_name'])) {
                    $query->where('last_name', 'LIKE', "%{$filters['building_owner_last_name']}%");
                }
            });
        }
    }

}
