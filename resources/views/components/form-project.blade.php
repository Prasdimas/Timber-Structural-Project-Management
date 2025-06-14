@props([
    'project' => null,
    'action',
    'method' => 'POST',
    'name',
         'errorBag' => 'default',
])
@php
    $bag = $errors->getBag($errorBag);
@endphp

{{-- Modal wrapper --}}
<x-modal :name="$name">
    <form method="POST" action="{{ $action }}" class="p-8 space-y-4"
          x-data="{
            // Determine if we are editing an existing project
            isEdit: '{{ $method }}' !== 'POST',

            // Reactive form fields
            name: '{{ old('name', $project?->name ?? '') }}',
            description: `{{ old('description', $project?->description ?? '') }}`,

            /**
             * Resets the form to its default state (used for create mode)
             */
            resetForm() {
                this.name = '';
                this.description = '';
            },

            /**
             * Fills form fields with provided data (used for edit mode)
             * @param {Object} data
             */
            fillForm(data) {
                this.name = data.name ?? '';
                this.description = data.description ?? '';
            },

            /**
             * Initializes the form depending on create/edit state
             * For edit: prefill the form
             * For create: reset form on modal open
             */
            init() {
                if (this.isEdit) {
                    this.fillForm({
                        name: '{{ addslashes($project?->name ?? '') }}',
                        description: `{{ addslashes($project?->description ?? '') }}`
                    });
                } else {
                    window.addEventListener('open-modal', (e) => {
                        if (e.detail === '{{ $name }}') {
                            this.resetForm();
                        }
                    });
                }
            }
        }"
        x-init="init()"
    >
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
  {{-- Modal heading --}}
        <h2 class="text-2xl font-semibold mb-2 text-center">
            {{ $project ? 'Edit Project' : 'New Project' }}
        </h2>

        {{-- Name field --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <x-text-input
                id="p-name"
                type="text"
                name="name"
                x-model="name"
                class="mt-1 block w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500"
            />
            <x-input-error class="mt-2" :messages="$bag->get('name')" />
        </div>

        {{-- Description field --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
                name="description"
                rows="3"
                x-model="description"
                class="mt-1 block w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500 resize-none"
            ></textarea>
            <x-input-error class="mt-2" :messages="$bag->get('description')" />
        </div>

        {{-- Action buttons: Cancel / Save --}}
        <div class="flex justify-end space-x-2">
            <button type="button"
                    class="px-3 py-2 rounded text-gray-700 hover:bg-gray-100"
                    x-on:click="if (!isEdit) resetForm(); $dispatch('close-modal', '{{ $name }}')">
                Cancel
            </button>

            <button type="submit"
                    class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                Save
            </button>
        </div>
    </form>
</x-modal>
