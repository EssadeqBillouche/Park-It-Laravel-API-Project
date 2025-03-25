<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Http\Requests\StoreParkingRequest;
use Illuminate\Http\JsonResponse;

class ParkingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $parkings = Parking::all();
        return response()->json([
            'status' => 'Success',
            'message' => 'Parkings retrieved successfully',
            'data' => [
                'parkings' => $parkings
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParkingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $parking = Parking::create($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Parking created successfully',
                'data' => [
                    'parking' => $parking
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Parking creation failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $parking = Parking::find($id);
        if (!$parking) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Parking not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Parking retrieved successfully',
            'data' => [
                'parking' => $parking
            ]
        ], 200);
    }

    /**
     * Display parkings by region.
     */
    public function showByRegion($id): JsonResponse
    {
        $parkings = Parking::where('region_id', $id)->get();
        if ($parkings->isEmpty()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'No parkings found for this region',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Parkings retrieved successfully',
            'data' => [
                'parkings' => $parkings
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreParkingRequest $request, $id): JsonResponse
    {
        try {
            $parking = Parking::find($id);
            if (!$parking) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Parking not found',
                    'data' => null
                ], 404);
            }

            $validated = $request->validated();
            $parking->update($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Parking updated successfully',
                'data' => [
                    'parking' => $parking
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Parking update failed: ' . $e->getMessage(),
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
            $parking = Parking::find($id);
            if (!$parking) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Parking not found',
                    'data' => null
                ], 404);
            }

            $parking->delete();
            return response()->json([
                'status' => 'Success',
                'message' => 'Parking deleted successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Parking deletion failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
