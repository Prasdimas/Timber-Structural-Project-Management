<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Project;
use App\Models\BuildingPart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can view the list of their own projects.
     */
    public function test_user_can_view_their_projects_index()
    {
        $user = User::factory()->create();
        Project::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/projects');

        $response->assertStatus(200);
        $response->assertSee(Project::first()->name);
    }

    /**
     * Test that a user can create a new project.
     */
    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'My Project',
            'description' => 'A test project'
        ];

        $response = $this->actingAs($user)->post('/projects', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'My Project', 'user_id' => $user->id]);
    }

    /**
     * Test that a user can view project details including its building parts.
     */
    public function test_user_can_view_project_detail_with_building_parts()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $part = BuildingPart::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)->get("/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertSee($part->name);
    }

    /**
     * Test that a user can update their own project.
     */
    public function test_user_can_update_a_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/projects/{$project->id}", [
            'name' => 'Updated Project',
            'description' => 'Updated Description'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'Updated Project']);
    }

    /**
     * Test that a user can soft delete a project along with its associated building parts.
     */
    public function test_user_can_soft_delete_project_and_its_building_parts()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $part = BuildingPart::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)->delete("/projects/{$project->id}");

        $response->assertRedirect();

        // Assert the project and building parts are soft deleted (not permanently removed)
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        $this->assertSoftDeleted('building_parts', ['id' => $part->id]);
    }
}
