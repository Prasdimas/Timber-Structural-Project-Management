{{-- Modal Edit Building --}}
<x-form-building-part
    :name="'edit-building-part-' . $part->id"
    method="PUT"
    :action="route('projects.building-parts.update', [$project, $part])"
    :project="$project"
    :buildingPart="$part"
    :showError="false"
    :errorBag="'updateBuildingPart_' . $part->id"
/>
{{-- Error Edit Building --}}
@if ($errors->getBag('updateBuildingPart_' . $part->id)->any())
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-building-part-{{ $part->id }}' }));
        });
    </script>
@endif
