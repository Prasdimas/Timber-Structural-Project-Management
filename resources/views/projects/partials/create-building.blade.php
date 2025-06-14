{{-- Modal Create Building --}}
<x-form-building-part
    name="create-building-part"
    :project="$project"
    :action="route('projects.building-parts.store', $project)"
    errorBag="createBuildingPart"
/>

{{-- Error Create Building --}}
@if ($errors->createBuildingPart->any())
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-building-part' }));
        });
    </script>
@endif
