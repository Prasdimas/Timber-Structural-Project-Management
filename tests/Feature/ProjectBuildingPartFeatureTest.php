<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\BuildingPart;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\SupplierSeeder;

class ProjectBuildingPartFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup sebelum setiap test dijalankan.
     * Membuat data supplier yang akan digunakan pada semua test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create supplier for all tests
        Supplier::create(['name' => 'Supplier CLT', 'material_type' => 'CLT']);
        Supplier::create(['name' => 'Supplier GLT', 'material_type' => 'GLT']);
    }

    /**
     * Test bahwa user bisa membuat building part pada project miliknya.
     */
    public function test_user_can_create_building_part()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('projects.building-parts.store', $project), [
            'name' => 'Wall Part',
            'building_part_type' => 'wall',
            'material_type' => 'CLT',
            'supplier_name' => 'Supplier CLT',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('building_parts', [
            'name' => 'Wall Part',
            'project_id' => $project->id,
        ]);
    }

    /**
     * Test bahwa user bisa mengupdate building part pada project miliknya.
     */
    public function test_user_can_update_building_part()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $part = BuildingPart::create([
            'project_id' => $project->id,
            'name' => 'Initial Part',
            'building_part_type' => 'beam',
            'material_type' => 'clt',
            'supplier_name' => 'Supplier CLT',
        ]);

        $response = $this->put(route('projects.building-parts.update', [$project, $part]), [
            'name' => 'Updated Part',
            'building_part_type' => 'beam',
            'material_type' => 'GLT',
            'supplier_name' => 'Supplier GLT',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('building_parts', [
            'id' => $part->id,
            'name' => 'Updated Part',
            'material_type' => 'glt', // pastikan lowercase sesuai kode penyimpanan
        ]);
    }

    /**
     * Test bahwa user bisa menghapus building part (soft delete).
     */
    public function test_user_can_delete_building_part()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $part = BuildingPart::create([
            'project_id' => $project->id,
            'name' => 'To Be Deleted',
            'building_part_type' => 'floor',
            'material_type' => 'clt',
            'supplier_name' => 'Supplier CLT',
        ]);

        $response = $this->delete(route('projects.building-parts.destroy', [$project, $part]));

        $response->assertRedirect();
        $this->assertSoftDeleted('building_parts', ['id' => $part->id]);
    }

    /**
     * Test validasi gagal saat material_type tidak sesuai dengan building_part_type.
     */
public function test_it_fails_validation_when_material_type_invalid_for_part_type()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $project = Project::factory()->create(['user_id' => $user->id]);

    Supplier::create([
        'name' => 'Supplier GLT',
        'material_type' => 'glt',
    ]);

    $response = $this->from(route('projects.show', $project))
        ->post(route('projects.building-parts.store', $project), [
            'name' => 'Invalid Part',
            'building_part_type' => 'wall', // hanya clt yang valid untuk wall
            'material_type' => 'GLT',
            'supplier_name' => 'Supplier GLT',
        ]);

    $response->assertRedirect(route('projects.show', $project));

    // ✅ Cek apakah error "material_type" muncul di error bag "createBuildingPart"
    $this->assertTrue(
        session()->get('errors')->getBag('createBuildingPart')->has('material_type'),
        'Expected material_type error in createBuildingPart error bag'
    );

    // ✅ Cek bahwa tidak tersimpan ke database
    $this->assertDatabaseMissing('building_parts', [
        'name' => 'Invalid Part',
    ]);
}

}
