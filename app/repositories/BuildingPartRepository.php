<?php
namespace App\Repositories;

use App\Models\BuildingPart;
use App\Models\Project;

class BuildingPartRepository implements BuildingPartRepositoryInterface
{
    public function create(Project $project, array $data)
    {
        return $project->buildingParts()->create($data);
    }

    public function update(array $data, int $id)
    {
        $part = BuildingPart::findOrFail($id);
        $part->update($data);
        return $part;
    }

    public function delete(int $id)
    {
        return BuildingPart::findOrFail($id)->delete();
    }
}
