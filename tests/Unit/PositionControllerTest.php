<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Parking;
use App\Models\Position;
use App\Http\Requests\StorePositionRequest;
use App\Http\Controllers\PositionController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PositionControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $parking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new PositionController();
        $this->parking = Parking::factory()->create();
    }

    public function test_index_returns_all_positions()
    {
        Position::factory()->count(3)->create(['parking_id' => $this->parking->id]);
        
        $response = $this->controller->index();
        $data = $response->original['data'];
        
        $this->assertEquals(3, count($data['Positions']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_store_creates_new_position()
    {
        $positionData = [
            'number' => 'A1',
            'hourly_rate' => 10.00,
            'status' => 'available',
            'parking_id' => $this->parking->id
        ];

        $request = new StorePositionRequest($positionData);
        $response = $this->controller->store($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('positions', $positionData);
    }

    public function test_show_by_parking_returns_positions()
    {
        Position::factory()->count(2)->create(['parking_id' => $this->parking->id]);
        
        $response = $this->controller->showByParking($this->parking->id);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotNull($response->original['data']['Positions']);
    }

    public function test_update_modifies_position()
    {
        $position = Position::factory()->create(['parking_id' => $this->parking->id]);
        $updateData = [
            'number' => 'B2',
            'hourly_rate' => 15.00,
            'status' => 'occupied',
            'parking_id' => $this->parking->id
        ];

        $request = new StorePositionRequest($updateData);
        $response = $this->controller->update($request, $position);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('positions', $updateData);
    }

    public function test_destroy_deletes_position()
    {
        $position = Position::factory()->create(['parking_id' => $this->parking->id]);
        
        $response = $this->controller->destroy($position);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }
}
