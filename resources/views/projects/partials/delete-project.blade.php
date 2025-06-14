{{-- Modal Delete Project --}}
    <x-modal name="confirm-delete-project-{{ $project->id }}" focusable>
       {{-- Form Confirmation --}}
        <form method="POST" action="{{ route('projects.destroy', [$project]) }}" class="p-6">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete this Project?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('The building part') }}
                <strong class="text-red-600">“{{ $project->name }}”</strong>
                {{ __('will be permanently removed.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Delete') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
