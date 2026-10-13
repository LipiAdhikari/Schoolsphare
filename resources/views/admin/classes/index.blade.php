<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Classes & Sections</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure academic grade levels, student sections, and classroom capacity.</p>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Classes & Sections Display (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Academic Classes Roster" description="Configured grade levels and their subdivided class sections." :padding="false">
                <div class="divide-y divide-slate-100">
                    @forelse ($classes as $class)
                        <div class="p-5 hover:bg-slate-50/50 transition-colors space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                        {{ $class->numeric_level }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">{{ $class->name }}</h3>
                                        <p class="text-xs text-slate-500">Level {{ $class->numeric_level }} &bull; {{ $class->sections_count }} {{ Str::plural('Section', $class->sections_count) }}</p>
                                    </div>
                                </div>
                                <x-badge variant="indigo" size="sm">
                                    {{ $class->subjects->count() }} {{ Str::plural('Subject', $class->subjects->count()) }}
                                </x-badge>
                            </div>

                            <!-- Sections List -->
                            <div class="pl-12">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    @forelse ($class->sections as $section)
                                        <div class="p-2.5 rounded-lg border border-slate-200 bg-white shadow-2xs flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $section->name }}</span>
                                                <span class="block text-[10px] text-slate-400">Cap: {{ $section->capacity }}</span>
                                            </div>
                                            @if ($section->room_number)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono">
                                                    Rm {{ $section->room_number }}
                                                </span>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">No sections created yet. Add one below.</span>
                                    @endforelse
                                </div>

                                <!-- Inline Add Section Form -->
                                <form method="POST" action="{{ route('admin.classes.sections.store', $class) }}" class="mt-3 flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                                    @csrf
                                    <input type="text" name="name" placeholder="New Section Name (e.g. Section C)" required class="text-xs rounded-md border border-slate-300 px-2.5 py-1.5 text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-40" />
                                    <input type="number" name="capacity" value="40" min="1" placeholder="Cap" required class="text-xs rounded-md border border-slate-300 px-2 py-1.5 text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-20" />
                                    <input type="text" name="room_number" placeholder="Room #" class="text-xs rounded-md border border-slate-300 px-2 py-1.5 text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-24" />
                                    <x-button type="submit" variant="secondary" size="xs">
                                        + Add Section
                                    </x-button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6">
                            <x-empty-state title="No classes registered" description="Create a new class using the form on the right to start." />
                        </div>
                    @endforelse
                </div>

                @if ($classes->hasPages())
                    <x-slot:footer>
                        {{ $classes->links() }}
                    </x-slot:footer>
                @endif
            </x-card>
        </div>

        <!-- Create Class Form (1 col) -->
        <div>
            <x-card title="Add New Class" description="Register a new academic class level.">
                <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Class Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Grade 10" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        @error('name')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="numeric_level" class="block text-xs font-semibold text-slate-700 mb-1">Numeric Level *</label>
                        <input type="number" name="numeric_level" id="numeric_level" value="{{ old('numeric_level', 1) }}" min="0" max="20" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <p class="text-[10px] text-slate-400 mt-1">Used for sorting and academic promotion order (e.g. 10 for Grade 10).</p>
                        @error('numeric_level')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Save Class
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
