<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\BuildingPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\BuildingPartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BuildingPartController extends Controller
{
    use AuthorizesRequests;

    protected BuildingPartService $service;

    public function __construct(BuildingPartService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $parts = $project->buildingParts()->latest()->paginate(10);

        return view('building_parts.index', compact('project', 'parts'));
    }

    /**
     * Store a newly created building part in storage.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validator = Validator::make($request->all(), [
            'name'               => ['required', 'string', 'max:255'],
            'building_part_type' => ['required', 'in:floor,wall,beam,column'],
            'material_type'      => ['required', 'string', 'max:3'],
            'supplier_name'      => ['required', 'string', 'max:255', 'exists:suppliers,name'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'createBuildingPart')
                ->withInput()
                ->with('showCreateBuildingPartModal', true);
        }

        $validated = $validator->validated();

        // Custom validation using service
        if (! $this->service->validateMaterial($validated['building_part_type'], $validated['material_type'])) {
            return back()
                ->withErrors(['material_type' => 'The selected material type is invalid for the given building part type.'], 'createBuildingPart')
                ->withInput()
                ->with('showCreateBuildingPartModal', true);
        }

        $this->service->create($project, $validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Building Part created.');
    }

    /**
     * Update the specified building part.
     */
    public function update(Request $request, Project $project, BuildingPart $buildingPart)
    {
        $this->authorize('update', $project);

        $errorBag = 'updateBuildingPart_' . $buildingPart->id;

        $validator = Validator::make($request->all(), [
            'name'               => ['required', 'string', 'max:255'],
            'building_part_type' => ['required', 'in:floor,wall,beam,column'],
            'material_type'      => ['required', 'string', 'max:3'],
            'supplier_name'      => ['required', 'string', 'max:255', 'exists:suppliers,name'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, $errorBag)
                ->withInput();
        }

        $validated = $validator->validated();

        if (! $this->service->validateMaterial($validated['building_part_type'], $validated['material_type'])) {
            return back()
                ->withErrors(['material_type' => 'The selected material type is invalid for the given building part type.'], $errorBag)
                ->withInput();
        }

        $this->service->update($validated, $buildingPart->id);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Building Part updated.');
    }

    /**
     * Remove the specified building part (soft delete).
     */
    public function destroy(Project $project, BuildingPart $buildingPart)
    {
        $this->authorize('delete', $project);

        $buildingPart->delete();

        return back()->with('success', 'Building Part deleted.');
    }
}
