<?php
namespace App\Repositories;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Project::where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project;
    }

    public function delete(Project $project): void
    {
        $project->buildingParts()->delete(); // delete related building parts
        $project->delete(); // soft delete
    }
}
