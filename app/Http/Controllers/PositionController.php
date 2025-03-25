<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Http\Requests\StorePositionRequest;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $positions = Position::all();
        return response()->json([
            'status' => 'Success',
            'message' => 'Positions retrieved successfully',
            'data' => [
                'positions' => $positions
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $position = Position::create($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Position created successfully',
                'data' => [
                    'position' => $position
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Position creation failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $position = Position::find($id);
        if (!$position) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Position not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Position retrieved successfully',
            'data' => [
                'position' => $position
            ]
        ], 200);
    }

    /**
     * Display positions by parking.
     */
    public function showByParking($id): JsonResponse
    {
        $positions = Position::where('parking_id', $id)->get();
        if ($positions->isEmpty()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'No positions found for this parking',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Positions retrieved successfully',
            'data' => [
                'positions' => $positions
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePositionRequest $request, Position $position): JsonResponse
    {
        try {
            $validated = $request->validated();
            $position->update($validated);

            return response()->json([
                'status' => 'Success',
                'message' => 'Position updated successfully',
                'data' => [
                    'position' => $position
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Position update failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position): JsonResponse
    {
        try {
            $position->delete();
            return response()->json([
                'status' => 'Success',
                'message' => 'Position deleted successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Position deletion failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
