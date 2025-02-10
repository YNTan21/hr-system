@section('site-title', 'Edit Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-6 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14 bg-white shadow-sm">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Edit Goal</h2> 

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Goal Edit Form -->
                <form action="{{ route('admin.kpi.update', ['position_id' => $position->id, 'id' => $goal->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Position Display -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position:</label>
                        <input type="text" value="{{ $position->position_name }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed" readonly disabled>
                    </div>

                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <!-- Goal Name -->
                        <div>
                            <label for="goal_name" class="block text-sm font-semibold text-gray-700 mb-2">Goal Name</label>
                            <input type="text" name="goal_name" id="goal_name" value="{{ old('goal_name', $goal->goal_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Goal Score -->
                        <div>
                            <label for="goal_score" class="block text-sm font-semibold text-gray-700 mb-2">Goal Score</label>
                            <input type="number" step="0.01" name="goal_score" id="goal_score" value="{{ old('goal_score', $goal->goal_score) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Goal Unit -->
                        <div>
                            <label for="goal_unit" class="block text-sm font-semibold text-gray-700 mb-2">Goal Unit</label>
                            <input type="text" name="goal_unit" id="goal_unit" value="{{ old('goal_unit', $goal->goal_unit) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Category Ranges -->
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Category</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Min</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Max</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach([
                                        'Failed',
                                        'Below Expectation',
                                        'Threshold',
                                        'Meet Target',
                                        'Excellence'
                                    ] as $index => $category)
                                        <tr class="border-b border-gray-200">
                                            <td class="px-4 py-3">
                                                <label class="text-sm text-gray-700">{{ $category }}:</label>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" 
                                                       name="category_{{ $index + 1 }}_min" 
                                                       class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       value="{{ old('category_' . ($index + 1) . '_min', $goal->category_score_ranges['category_' . ($index + 1)]['min'] ?? '') }}">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" 
                                                       name="category_{{ $index + 1 }}_max" 
                                                       class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       required 
                                                       value="{{ old('category_' . ($index + 1) . '_max', $goal->category_score_ranges['category_' . ($index + 1)]['max'] ?? '') }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.kpi.index') }}" 
                           class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Save Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
