{{-- Modal View Detail Building  --}}
<x-form-building-part
        :name="'detail-building-part-' . $part->id"
        method="PUT"
        :action="route('projects.building-parts.update', [$project, $part])"
        :project="$project"
        :buildingPart="$part"
        :readonly="true"
    />
