<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Position;
use App\Models\Reservation;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Controllers\ReservationController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $user;
    private $position;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ReservationController();
        $this->user = User::factory()->create();
        $this->position = Position::factory()->create();
    }

    public function test_index_returns_all_reservations()
    {
        Reservation::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'position_id' => $this->position->id
        ]);
        
        $response = $this->controller->index();
        $data = $response->original['data'];
        
        $this->assertEquals(3, count($data['Reservations']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_store_creates_new_reservation()
    {
        $reservationData = [
            'start_time' => now(),
            'end_time' => now()->addHours(2),
            'status' => 'active',
            'total_price' => 50.00,
            'notes' => 'Test reservation',
            'user_id' => $this->user->id,
            'position_id' => $this->position->id
        ];

        $request = new StoreReservationRequest($reservationData);
        $response = $this->controller->store($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('reservations', $reservationData);
    }

    public function test_store_prevents_double_booking()
    {
        $existingReservation = Reservation::factory()->create([
            'start_time' => now(),
            'end_time' => now()->addHours(2),
            'position_id' => $this->position->id,
            'status' => 'active'
        ]);

        $newReservationData = [
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(3),
            'status' => 'active',
            'position_id' => $this->position->id,
            'user_id' => $this->user->id,
            'total_price' => 50.00,
            'notes' => 'Test reservation'
        ];

        $request = new StoreReservationRequest($newReservationData);
        $response = $this->controller->store($request);
        
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function test_update_modifies_reservation()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'position_id' => $this->position->id
        ]);

        $updateData = [
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'updated',
            'total_price' => 75.00,
            'notes' => 'Updated reservation',
            'user_id' => $this->user->id,
            'position_id' => $this->position->id
        ];

        $request = new StoreReservationRequest($updateData);
        $response = $this->controller->update($request, $reservation);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('reservations', $updateData);
    }

    public function test_destroy_cancels_reservation()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'position_id' => $this->position->id,
            'status' => 'active'
        ]);
        
        $response = $this->controller->destroy($reservation->id);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled'
        ]);
    }
}
