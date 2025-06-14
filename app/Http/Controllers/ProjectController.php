<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    protected ProjectService $service;

    /**
     * Inject the ProjectService dependency.
     *
     * @param ProjectService $service
     */
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a paginated list of projects owned by the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = $this->service->getProjectsForUser(auth()->id());

        return view('projects.index', compact('projects'));
    }

    /**
     * Handle the creation of a new project.
     *
     * Validates input, creates a project via the service, and redirects.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        // Return back with errors if validation fails
        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'createProject')
                ->withInput()
                ->with('showCreateProjectModal', true);
        }

        // Create the project with validated data and authenticated user ID
        $this->service->create([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        // Redirect to projects index with success message
        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display a specific project details.
     *
     * Checks authorization and loads related building parts paginated.
     *
     * @param Project $project
     * @return \Illuminate\View\View
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $parts = $project->buildingParts()->latest()->paginate(10);

        return view('projects.show', compact('project', 'parts'));
    }

    /**
     * Update an existing project.
     *
     * Validates input, checks authorization, updates via service, and redirects.
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $errorBag = 'updateProject_' . $project->id;

        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        // Return back with validation errors if failed
        if ($validator->fails()) {
            return back()
                ->withErrors($validator, $errorBag)
                ->withInput()
                ->with('showUpdateProjectModal', 'edit-project-' . $project->id);
        }

        // Update the project using validated data
        $this->service->update($project, $validator->validated());

        // Redirect with success message
        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Soft delete a project along with its related building parts.
     *
     * Checks authorization and performs deletion via service.
     *
     * @param Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->service->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
