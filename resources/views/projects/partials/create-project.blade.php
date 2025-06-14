{{-- Modal Create Project --}}
<x-form-project
    name="create-project"
    :action="route('projects.store')"
    errorBag="createProject"
/>

{{-- Error Create Project  --}}
@if ($errors->createProject->any())
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-project' }));
        });
    </script>
@endif
