<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Region;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Controllers\RegionController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegionControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new RegionController();
    }

    public function test_index_returns_all_regions()
    {
        Region::factory()->count(3)->create();
        
        $response = $this->controller->index();
        $data = $response->original['data'];
        
        $this->assertEquals(3, count($data['regions']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_store_creates_new_region()
    {
        $regionData = [
            'name' => 'Test Region',
            'description' => 'Test Description',
            'status' => 'active'
        ];

        $request = new StoreRegionRequest($regionData);
        $response = $this->controller->store($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('regions', $regionData);
    }

    public function test_show_returns_region()
    {
        $region = Region::factory()->create();
        
        $response = $this->controller->show($region->id);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($region->id, $response->original['data']['region']->id);
    }

    public function test_update_modifies_region()
    {
        $region = Region::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'status' => 'inactive'
        ];

        $request = new StoreRegionRequest($updateData);
        $response = $this->controller->update($request, $region);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('regions', $updateData);
    }

    public function test_destroy_deletes_region()
    {
        $region = Region::factory()->create();
        
        $response = $this->controller->destroy($region);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }
}
