<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Http\Requests\StoreRegionRequest;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $regions = Region::all();
        return response()->json([
            'status' => 'Success',
            'message' => 'Regions retrieved successfully',
            'data' => [
                'regions' => $regions
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $region = Region::create($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Region created successfully',
                'data' => [
                    'region' => $region
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Region creation failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $region = Region::find($id);
        if (!$region) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Region not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Region retrieved successfully',
            'data' => [
                'region' => $region
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRegionRequest $request, $id): JsonResponse
    {
        try {
            $region = Region::find($id);
            if (!$region) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Region not found',
                    'data' => null
                ], 404);
            }

            $validated = $request->validated();
            $region->update($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Region updated successfully',
                'data' => [
                    'region' => $region
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Region update failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $region = Region::find($id);
            if (!$region) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Region not found',
                    'data' => null
                ], 404);
            }

            $region->delete();
            return response()->json([
                'status' => 'Success',
                'message' => 'Region deleted successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Region deletion failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
