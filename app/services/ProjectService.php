<?php
namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;

class ProjectService
{
    protected ProjectRepositoryInterface $repo;

    public function __construct(ProjectRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getProjectsForUser(int $userId)
    {
        return $this->repo->paginateByUser($userId);
    }

    public function create(array $data): Project
    {
        return $this->repo->create($data);
    }

    public function update(Project $project, array $data): Project
    {
        return $this->repo->update($project, $data);
    }

    public function delete(Project $project): void
    {
        $this->repo->delete($project);
    }
}
