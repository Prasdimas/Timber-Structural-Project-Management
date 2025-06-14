<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\BuildingPart;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can view their projects index page.
     */
    public function test_user_can_view_their_projects_index()
    {
        $user = User::factory()->create();
        Project::factory()->count(15)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertStatus(200)
            ->assertSeeText('Projects'); // asumsi ada kata Projects di view
    }

    /**
     * Test that a user can create/store a new project.
     */
    public function test_user_can_store_project()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'New Project',
            'description' => 'Project description',
        ];

        $response = $this->actingAs($user)
            ->post(route('projects.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test that a user can view details of their own project.
     */
    public function test_user_can_view_project_details()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('projects.show', $project))
            ->assertStatus(200)
            ->assertSeeText($project->name);
    }

    /**
     * Test that a user can update their own project.
     */
    public function test_user_can_update_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $data = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($user)
            ->put(route('projects.update', $project), $data);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ]);
    }

    /**
     * Test that deleting a project soft deletes its related building parts.
     */
    public function test_user_can_delete_project_and_soft_delete_building_parts()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $buildingPart = \App\Models\BuildingPart::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)
            ->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        $this->assertSoftDeleted('building_parts', ['id' => $buildingPart->id]);
    }

    /**
     * Test that a user cannot view building parts of another user's project.
     */
    public function test_user_cannot_view_building_parts_of_others_project()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $project = Project::factory()->for($otherUser)->create();
        $buildingParts = BuildingPart::factory()->for($project)->count(2)->create();

        $this->actingAs($user)
            ->get(route('projects.show', $project))
            ->assertStatus(403); // forbidden access
    }

    /**
     * Test that a user can create a building part for their own project.
     * Pastikan supplier sudah ada di database agar validasi berhasil.
     */
    public function test_user_can_create_building_part_for_own_project()
    {
        $user = User::factory()->create();

        // Buat supplier yang sesuai di DB (harus ada)
        Supplier::create([
            'name' => 'Sodra',        // pastikan nama supplier sama dengan input
            'material_type' => 'clt', // pastikan material type valid dan sesuai
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('projects.building-parts.store', $project), [
            'name' => 'Wall Part',
            'building_part_type' => 'wall',
            'material_type' => 'clt',       // lowercase sesuai validation
            'supplier_name' => 'Sodra',     // harus sama dengan supplier di DB
        ]);

        $response->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('building_parts', [
            'name' => 'Wall Part',
            'project_id' => $project->id,
        ]);
    }

    /**
     * Test that a user cannot create a building part for another user's project.
     */
    public function test_user_cannot_create_building_part_for_others_project()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $project = Project::factory()->for($otherUser)->create();

        $data = [
            'name' => 'New Building Part',
            'description' => 'Description',
        ];

        $this->actingAs($user)
            ->post(route('projects.building-parts.store', $project), $data)
            ->assertStatus(403);
    }
}
