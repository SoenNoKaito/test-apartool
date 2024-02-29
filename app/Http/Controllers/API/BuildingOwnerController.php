<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\BuildingOwnerRepository;
use Illuminate\Http\JsonResponse;

class BuildingOwnerController extends Controller
{

    protected BuildingOwnerRepository $buildingOwnerRepository;

    public function __construct(BuildingOwnerRepository $buildingOwnerRepository)
    {
        $this->buildingOwnerRepository = $buildingOwnerRepository;
    }

    public function disable($id): JsonResponse
    {
        $this->buildingOwnerRepository->disableBuildingOwner($id);

        return $this->sendResponse([], 'Building owner and related entities disabled successfully.');
    }
}
