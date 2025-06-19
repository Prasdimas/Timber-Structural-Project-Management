@props([
    'readonly' => false,
    'name',                 // e.g., "create-building-part" or "edit-building-part"
    'action',               // The URL the form submits to
    'method' => 'POST',     // HTTP method: "POST" for create, "PUT" for update
    'project',              // Instance of the Project model (required)
    'buildingPart' => null, // Instance of BuildingPart model or null (null when creating)
    'errorBag' => 'default' // Named error bag used for validation errors
])

@php
    // Get the appropriate error bag passed from the component
    $bag = $errors->getBag($errorBag);
@endphp

{{-- Modal for Creating or Editing a Building Part --}}
<x-modal :name="$name">
    <script>
        /**
         * Alpine.js component for handling the Building Part form.
         *
         * @param {Object|null} initial - Initial form data (null for create, object for edit).
         * @param {Boolean} isEdit - Whether the form is in edit mode.
         * @param {Boolean} readonly - Whether the form is in read-only mode.
         */
        function buildingPartForm(initial, isEdit = false, readonly) {
            return {
                isEdit: isEdit,
                readonly: readonly,

                // Reactive form fields
                form: {
                    name:        initial?.name               ?? '',
                    partType:    initial?.building_part_type ?? '',
                    material:    initial?.material_type      ?? '',
                    supplier:    initial?.supplier_name      ?? '',
                },

                /**
                 * Get all suppliers from the global Alpine store (projectData).
                 * Expected to be an array of { name, material_type }.
                 */
                get suppliers() {
                    return Alpine.store('projectData')?.suppliers || [];
                },
                get partTypes() {
                    return Alpine.store('projectData')?.suppliers || [];
                },

                /**
                 * Return valid materials based on the selected building part type.
                 */
                get materials() {
                    switch (this.form.partType) {
                        case 'floor':
                        case 'wall':   return ['CLT'];
                        case 'beam':   return ['CLT', 'GLT'];
                        case 'column': return ['GLT'];
                        default:       return [];
                    }
                },

                /**
                 * Filter suppliers based on the selected material type.
                 */
                get filteredSuppliers() {
                    if (!this.form.material) return this.suppliers;
                    return this.suppliers.filter(s =>
                        s.material_type.toLowerCase() === this.form.material.toLowerCase()
                    );
                },

                /**
                 * Sync material and supplier fields if material becomes invalid for selected part type.
                 */
                syncMaterial() {
                    if (!this.materials.includes(this.form.material)) {
                        this.form.material = '';
                        this.form.supplier = '';
                    }
                },

                /**
                 * Reset the form to default values (used for create mode or cancel).
                 */
                resetForm() {
                    this.form = {
                        name:        '',
                        partType:    '',
                        material:    '',
                        supplier:    '',
                    };
                },
            }
        }
    </script>
   {{-- Building Part Form --}}
    <form method="POST" action="{{ $action }}"
          class="p-6 space-y-6"
          x-data="buildingPartForm({{ json_encode($buildingPart) }}, {{ $buildingPart ? 'true' : 'false' }}, {{ $readonly ? 'true' : 'false' }})"
    >
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        {{-- Modal Heading --}}
        <h2 class="text-2xl font-bold text-center">
            @if ($readonly)
                Detail Building Part
            @else
                {{ $buildingPart ? 'Edit Building Part' : 'Add Building Part' }}
            @endif
        </h2>
        {{-- Name Input --}}
        <div>
            <x-input-label for="bp-name" :value="__('Name')" />
            <x-text-input id="bp-name" name="name" type="text" :readonly="$readonly"
                          class="block mt-1 w-full focus:border-green-500"
                          x-model="form.name" />
            <x-input-error class="mt-2" :messages="$bag->get('name')" x-show="!readonly" />
        </div>

        {{-- Building Part Type Dropdown --}}
        <div>
            <x-input-label for="bp-type" :value="__('Building Part Type')" />
            <select id="bp-type" name="building_part_type"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring
                           focus:ring-green-200 focus:ring-opacity-50"
                    x-model="form.partType" :disabled="readonly"
                    @change="syncMaterial()">
                <option value="" disabled>-- Select Part Type --</option>
                <template x-for="opt in ['floor','wall','beam','column']" :key="opt">
                    <option :value="opt" x-text="opt.charAt(0).toUpperCase()+opt.slice(1)"  :selected="form.partType === opt"></option>
                </template>
            </select>
            <x-input-error class="mt-2" x-show="!readonly" :messages="$bag->get('building_part_type')" />
        </div>

        {{-- Material Dropdown --}}
        <div>
            <x-input-label for="bp-material" :value="__('Material')" />
            <select id="bp-material" name="material_type" :disabled="readonly"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring
                           focus:ring-green-200 focus:ring-opacity-50"
                    x-model="form.material">
                <option value="" disabled>-- Select Material --</option>
                <template x-for="mat in materials" :key="mat">
                    <option :value="mat" x-text="mat" :selected="form.material === mat"></option>
                </template>
            </select>
            <x-input-error class="mt-2" x-show="!readonly" :messages="$bag->get('material_type')" />
        </div>

        {{-- Supplier Dropdown --}}
<div>
    <x-input-label for="bp-supplier" :value="__('Supplier')" />
    <select id="bp-supplier" name="supplier_name"
            :disabled="readonly || !form.material"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring
                   focus:ring-green-200 focus:ring-opacity-50"
            x-model="form.supplier">
        <option value="" disabled>-- Select Supplier --</option>
        <template x-for="sup in filteredSuppliers" :key="sup.id">
            <option :value="sup.name" x-text="sup.name" :selected="form.supplier === sup.name"></option>
        </template>
    </select>
    <x-input-error class="mt-2" x-show="!readonly" :messages="$bag->get('supplier_name')" />
</div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-2">
            @if ($readonly)
                <button type="button" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-100"
                        x-on:click="$dispatch('close-modal', '{{ $name }}')">
                    Close
                </button>
            @else
                <button type="button" class="px-3 py-2 rounded text-gray-700 hover:bg-gray-100"
                        x-on:click="if (!isEdit) resetForm(); $dispatch('close-modal', '{{ $name }}')">
                    Cancel
                </button>

                <button type="submit" x-on:click="readonly = false"
                        class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    Save
                </button>
            @endif
        </div>
    </form>
</x-modal>
