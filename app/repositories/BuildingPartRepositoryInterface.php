<?php
namespace App\Repositories;

use App\Models\Project;

interface BuildingPartRepositoryInterface
{
    public function create(Project $project, array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
}
