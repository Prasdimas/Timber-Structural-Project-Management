{{-- Notifications --}}
@if (session('success') && trim(session('success')) !== '')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success-modal' }));
        });
    </script>

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
    <main class="flex-1 overflow-auto mx-auto w-full p-2">
        <!-- Page title -->
        <p class="pb-5">Projects</p>

        <!-- Header: Title and New Project button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 select-text pt-1">Projects</h2>
            </div>
            <div>
                <!-- Open the create project modal -->
                <button
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-project')"
                    type="button"
                    class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-gray-700 font-semibold hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition">
                    New Project
                </button>
            </div>
        </div>

        <!-- Table section listing all projects -->
        <section class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full table-fixed border-collapse">
                <thead class="bg-green-50 border-b border-green-200">
                    <tr>
                        <!-- Table headers -->
                        <th scope="col" class="w-1/3 text-left px-6 py-4 text-sm font-semibold select-none">Name</th>
                        <th scope="col" class="w-1/3 text-left px-6 py-4 text-sm font-semibold select-none">Description</th>
                        <th scope="col" class="w-1/3 text-left px-6 py-4 text-sm font-semibold text-green-700 select-none pr-10">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Loop through projects -->
                    @forelse ($projects as $project)
                        <tr class="hover:bg-green-50 focus-within:bg-green-100">
                            <!-- Project name -->
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $project->name }}</td>
                            <!-- Project description -->
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $project->description }}</td>
                            <td class="px-6 py-4 text-sm pr-10">
                                <!-- View project -->
                                <a href="{{ route('projects.show', $project) }}" class="text-green-600 hover:text-green-900 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    View |
                                </a>

                                <!-- Edit project: triggers edit modal -->
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'edit-project-{{ $project->id }}')"
                                    class="text-green-600 hover:text-green-900 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    Edit |
                                </button>

                                <!-- Include edit modal partial -->
                                @include('projects.partials.edit-project', ['project' => $project])

                                <!-- Delete project: triggers confirmation modal -->
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-delete-project-{{ $project->id }}')"
                                    class="text-green-600 hover:text-green-800 font-medium focus:outline-none focus:ring-2 focus:ring-green-500 rounded">
                                    Delete
                                </button>

                                <!-- Include delete confirmation modal partial -->
                                @include('projects.partials.delete-project', ['project' => $project])
                            </td>
                        </tr>
                    @empty
                        <!-- Message when no projects exist -->
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500">
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- Pagination section -->
        <div class="mt-6 flex justify-center">
            @if ($projects->hasPages())
                <div class="mt-6 flex justify-center space-x-1 text-base">
                    {{-- Previous Page Link --}}
                    @if ($projects->onFirstPage())
                        <span class="text-gray-300 flex items-center justify-center">
                            <span class="material-icons text-lg">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $projects->previousPageUrl() }}" class="text-gray-500 hover:text-green-600 flex items-center justify-center">
                            <span class="material-icons text-lg">chevron_left</span>
                        </a>
                    @endif

                    {{-- Page number links --}}
                    @foreach ($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                        @if ($page == $projects->currentPage())
                            <span class="bg-green-600 text-white px-3 py-1.5 rounded-full">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="text-gray-700 hover:text-green-600 px-3 py-1.5 rounded">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($projects->hasMorePages())
                        <a href="{{ $projects->nextPageUrl() }}" class="text-gray-500 hover:text-green-600 flex items-center justify-center">
                            <span class="material-icons text-lg">chevron_right</span>
                        </a>
                    @else
                        <span class="text-gray-300 flex items-center justify-center">
                            <span class="material-icons text-lg">chevron_right</span>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </main>
</x-app-layout>
