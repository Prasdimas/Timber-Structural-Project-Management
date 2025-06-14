<?php

namespace Tests\Unit;

use App\Models\BuildingPart;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingPartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a building part can be created successfully with valid data.
     */
    public function test_building_part_can_be_created_with_valid_data()
    {
        $project = Project::factory()->create();

        $buildingPart = BuildingPart::create([
            'project_id' => $project->id,
            'name' => 'Beam 001',
            'building_part_type' => 'beam',
            'material_type' => 'CLT',
            'supplier_name' => 'Sample Supplier',
        ]);

        $this->assertDatabaseHas('building_parts', [
            'id' => $buildingPart->id,
            'name' => 'Beam 001',
        ]);
    }
}
