<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuildingResource;
use App\Repositories\BuildingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    private BuildingRepository $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
    }

    public function getListWithFilters(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'active' => 'nullable|boolean',
            'building_owner_name' => 'nullable|string',
            'building_owner_last_name' => 'nullable|string',
            'perPage' => 'nullable|integer|max:30',
        ]);

        // Add custom validation rule for JSON format
        $validator->after(function ($validator) use ($request) {
            if (!empty($request->getContent())) {
                json_decode($request->getContent(), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('body', 'Invalid JSON format');
                }
            }
        });

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $filters = $request->only(['name', 'active', 'building_owner_name', 'building_owner_last_name']);
        $perPage = $request->input('perPage', 10);

        $buildings = $this->buildingRepository->getAllWithFilters($filters, $perPage);

        return BuildingResource::collection($buildings)->response();
    }
}
