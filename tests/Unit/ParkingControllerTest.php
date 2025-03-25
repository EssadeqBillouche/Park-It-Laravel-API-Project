<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Region;
use App\Models\Parking;
use App\Http\Requests\StoreParkingRequest;
use App\Http\Controllers\ParkingController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $region;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ParkingController();
        $this->region = Region::factory()->create();
    }

    public function test_index_returns_all_parkings()
    {
        Parking::factory()->count(3)->create(['region_id' => $this->region->id]);
        
        $response = $this->controller->index();
        $data = $response->original['data'];
        
        $this->assertEquals(3, count($data['Parkings']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_store_creates_new_parking()
    {
        $parkingData = [
            'name' => 'Test Parking',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'total_position' => 10,
            'status' => 'active',
            'region_id' => $this->region->id
        ];

        $request = new StoreParkingRequest($parkingData);
        $response = $this->controller->store($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('parkings', $parkingData);
    }

    public function test_show_by_region_returns_parkings()
    {
        Parking::factory()->count(2)->create(['region_id' => $this->region->id]);
        
        $response = $this->controller->showByRegion($this->region->id);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotNull($response->original['data']['Parkings']);
    }

    public function test_update_modifies_parking()
    {
        $parking = Parking::factory()->create(['region_id' => $this->region->id]);
        $updateData = [
            'name' => 'Updated Parking',
            'description' => 'Updated Description',
            'address' => 'Updated Address',
            'total_position' => 15,
            'status' => 'inactive',
            'region_id' => $this->region->id
        ];

        $request = new StoreParkingRequest($updateData);
        $response = $this->controller->update($request, $parking);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('parkings', $updateData);
    }

    public function test_destroy_deletes_parking()
    {
        $parking = Parking::factory()->create(['region_id' => $this->region->id]);
        
        $response = $this->controller->destroy($parking);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseMissing('parkings', ['id' => $parking->id]);
    }
}
