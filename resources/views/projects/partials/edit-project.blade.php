{{-- Modal Edit Project --}}
<x-form-project
    :name="'edit-project-' . $project->id"
    :project="$project"
    method="PUT"
    :action="route('projects.update', $project)"
errorBag="updateProject_{{ $project->id }}"
/>

{{-- Error Edit Project --}}
@if ($errors->getBag('updateProject_' . $project->id)->any())
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-project-{{ $project->id }}' }));
        });
    </script>
@endif


