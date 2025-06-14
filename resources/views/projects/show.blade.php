<script>
    window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success-modal' }));
        });
    document.addEventListener('alpine:init', () => {
        Alpine.store('projectData', {
            suppliers: [],

            async init() {
                try {
                    const response = await fetch('/api/suppliers');
                    this.suppliers = await response.json();
                } catch (error) {
                    console.error('Failed to load suppliers:', error);
                }
            }
        });

        // Load immediately Alpine
        Alpine.store('projectData').init();
    });
</script>

{{-- Notifications --}}
@if (session('success') && trim(session('success')) !== '')
    <x-modal name="success-modal">
        <div class="p-6">
            <h2 class="text-lg font-bold text-green-600">Success</h2>
            <p>{{ session('success') }}</p>
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'success-modal')"
                class="mt-4 px-4 py-2 bg-green-600 text-white rounded">
                Close
            </button>
        </div>
    </x-modal>
@endif


<x-app-layout>
    <main class="flex-1 overflow-auto mx-auto w-full p-2" x-data>

        <!-- Breadcrumb navigation -->
        <p class="pb-3 cursor-default">
            <a href="/projects" class="text-green-400">Projects</a> / {{ $project->name }}
        </p>

        <!-- Page title -->
        <h2 class="text-3xl font-extrabold text-gray-900 select-text cursor-default">Project Details</h2>
        <p class="pb-5 text-green-500 cursor-default">Manage project components and details</p>

        <!-- Display project name and description -->
        <div class="w-2/5">
            <!-- Project Name (read-only input) -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Project Name')" />
                <x-text-input id="name" type="text" name="name" :value="$project->name" class="block mt-1 w-full cursor-default" readonly />
            </div>

            <!-- Project Description (read-only textarea) -->
            <div class="mb-4">
                <x-input-label for="description" :value="__('Description')" />
                <textarea
                    id="description"
                    name="description"
                    rows="2"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 resize-none cursor-default"
                    readonly
                >{{ $project->description ?? 'No description provided.' }}</textarea>
            </div>
        </div>


        <!-- Header: Building Parts + Add button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5 gap-4">
            <div>
                <h3 class="text-xl font-bold mb-3 text-gray-800">Building Parts</h3>
            </div>
            <div>
                <!-- Open create building part modal -->
                <button
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-building-part')"
                    type="button"
                    class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-gray-700 font-semibold hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition">
                    Add Building Part
                </button>

                <!-- Include modal partial for creating building part -->
                @include('projects.partials.create-building')
            </div>
        </div>

        <!-- Building parts table -->
        <section class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full table-fixed border-collapse">
                <thead class="bg-green-50 border-b border-green-200">
                    <tr>
                        <th class="w-1/6 text-left px-6 py-4 text-sm font-semibold">#</th>
                        <th class="w-1/5 text-left px-6 py-4 text-sm font-semibold">Name</th>
                        <th class="w-1/6 text-left px-6 py-4 text-sm font-semibold">Type</th>
                        <th class="w-1/6 text-left px-6 py-4 text-sm font-semibold">Material</th>
                        <th class="w-1/6 text-left px-6 py-4 text-sm font-semibold">Supplier</th>
                        <th class="w-1/6 text-left px-6 py-4 text-sm font-semibold text-green-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Loop through each building part -->
                    @forelse ($parts as $part)
                        <tr class="hover:bg-green-50 focus-within:bg-green-100">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $part->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $part->building_part_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $part->material_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $part->supplier_name }}</td>
                            <td class="px-6 py-4 text-sm pr-10">

                                <!-- View building part button -->
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'detail-building-part-{{ $part->id }}')"
                                    class="text-green-600 hover:text-green-900 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    View |
                                </button>
                                @include('projects.partials.detail-building', ['project' => $project])

                                <!-- Edit building part button -->
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'edit-building-part-{{ $part->id }}')"
                                    class="text-green-600 hover:text-green-900 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    Edit |
                                </button>
                                @include('projects.partials.edit-building', ['part' => $part])

                                <!-- Delete building part button -->
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-delete-building-part-{{ $part->id }}')"
                                    class="text-green-600 hover:text-green-800 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    Delete
                                </button>
                                @include('projects.partials.delete-building', ['project' => $project])
                            </td>
                        </tr>
                    @empty
                        <!-- Message shown when no building parts exist -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-500">
                                No building parts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        @if ($parts->hasPages())
    <div class="mt-6 flex justify-center space-x-1 text-base">
        {{-- Previous Page --}}
        @if ($parts->onFirstPage())
            <span class="text-gray-300 flex items-center justify-center">
                <span class="material-icons text-lg">chevron_left</span>
            </span>
        @else
            <a href="{{ $parts->previousPageUrl() }}" class="text-gray-500 hover:text-green-600 flex items-center justify-center">
                <span class="material-icons text-lg">chevron_left</span>
            </a>
        @endif

        {{-- Page Number Links --}}
        @foreach ($parts->getUrlRange(1, $parts->lastPage()) as $page => $url)
            @if ($page == $parts->currentPage())
                <span class="bg-green-600 text-white px-3 py-1.5 rounded-full">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="text-gray-700 hover:text-green-600 px-3 py-1.5 rounded">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($parts->hasMorePages())
            <a href="{{ $parts->nextPageUrl() }}" class="text-gray-500 hover:text-green-600 flex items-center justify-center">
                <span class="material-icons text-lg">chevron_right</span>
            </a>
        @else
            <span class="text-gray-300 flex items-center justify-center">
                <span class="material-icons text-lg">chevron_right</span>
            </span>
        @endif
    </div>
@endif

    </main>
</x-app-layout>
