<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\BuildingOwnerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BuildingOwnerController extends Controller
{

    protected BuildingOwnerRepository $buildingOwnerRepository;

    public function __construct(BuildingOwnerRepository $buildingOwnerRepository)
    {
        $this->buildingOwnerRepository = $buildingOwnerRepository;
    }

    public function disable($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $this->buildingOwnerRepository->disableBuildingOwner($id);

        return $this->sendResponse([], 'Building owner and related entities disabled successfully.');
    }
}
