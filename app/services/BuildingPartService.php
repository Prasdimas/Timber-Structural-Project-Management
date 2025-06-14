<?php
namespace App\Services;

use App\Models\Project;
use App\Repositories\BuildingPartRepositoryInterface;

class BuildingPartService
{
    protected BuildingPartRepositoryInterface $repository;

    public function __construct(BuildingPartRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function validateMaterial(string $type, string $material): bool
    {
        $material = strtolower($material);
        $validMaterials = match ($type) {
            'floor', 'wall' => ['clt'],
            'beam'          => ['clt', 'glt'],
            'column'        => ['glt'],
            default         => [],
        };

        return in_array($material, $validMaterials);
    }

    public function create(Project $project, array $data)
    {
        $data['material_type'] = strtoupper($data['material_type']);
        return $this->repository->create($project, $data);
    }

    public function update(array $data, int $id)
    {
        $data['material_type'] = strtolower($data['material_type']);
        return $this->repository->update($data, $id);
    }
}
