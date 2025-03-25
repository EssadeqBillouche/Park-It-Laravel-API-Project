<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Requests\StoreReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $reservations = Reservation::all();
        return response()->json([
            'status' => 'Success',
            'message' => 'Reservations retrieved successfully',
            'data' => [
                'reservations' => $reservations
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            return DB::transaction(function () use ($validated) {
                // Check for overlapping reservations
                $existingReservation = Reservation::where('position_id', $validated['position_id'])
                    ->where('status', 'active')
                    ->where(function ($query) use ($validated) {
                        $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                            ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                            ->orWhere(function ($q) use ($validated) {
                                $q->where('start_time', '<', $validated['start_time'])
                                    ->where('end_time', '>', $validated['end_time']);
                            });
                    })
                    ->exists();

                if ($existingReservation) {
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'This position is already reserved for the selected time period',
                        'data' => null
                    ], 409);
                }

                $reservation = Reservation::create($validated);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Reservation created successfully',
                    'data' => [
                        'reservation' => $reservation
                    ]
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Reservation creation failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Reservation not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'Success',
            'message' => 'Reservation retrieved successfully',
            'data' => [
                'reservation' => $reservation
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreReservationRequest $request, Reservation $reservation): JsonResponse
    {
        try {
            $validated = $request->validated();

            return DB::transaction(function () use ($validated, $reservation) {
                // Check for overlapping reservations excluding current one
                $existingReservation = Reservation::where('position_id', $validated['position_id'])
                    ->where('status', 'active')
                    ->where('id', '!=', $reservation->id)
                    ->where(function ($query) use ($validated) {
                        $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                            ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                            ->orWhere(function ($q) use ($validated) {
                                $q->where('start_time', '<', $validated['start_time'])
                                    ->where('end_time', '>', $validated['end_time']);
                            });
                    })
                    ->exists();

                if ($existingReservation) {
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'This position is already reserved for the selected time period',
                        'data' => null
                    ], 409);
                }

                $reservation->update($validated);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Reservation updated successfully',
                    'data' => [
                        'reservation' => $reservation
                    ]
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Reservation update failed: ' . $e->getMessage(),
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
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Reservation not found',
                    'data' => null
                ], 404);
            }

            $reservation->update(['status' => 'cancelled']);

            return response()->json([
                'status' => 'Success',
                'message' => 'Reservation cancelled successfully',
                'data' => [
                    'reservation' => $reservation
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Reservation cancellation failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
