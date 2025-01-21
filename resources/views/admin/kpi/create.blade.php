@section('site-title', 'Create Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800 dark:text-white">Create Goal</h2> 
                </div>

                <!-- Goal Creation Form -->
                <form action="{{ route('admin.kpi.store', ['position_id' => $positions->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="goal_type" value="monthly">

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 gap-6 mb-6 max-w-3xl mx-auto">
                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Position:</label>
                            <input type="text" value="{{ $positions->position_name }}" 
                                   class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:text-gray-300 cursor-not-allowed" 
                                   readonly disabled>
                        </div>

                        <!-- Goal Name -->
                        <div>
                            <label for="goal_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Goal Name:</label>
                            <input type="text" name="goal_name" id="goal_name" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   required>
                        </div>

                        <!-- Goal Score and Unit Row -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Goal Score -->
                            <div>
                                <label for="goal_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Goal Score:</label>
                                <input type="number" step="0.01" name="goal_score" id="goal_score" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                       required>
                            </div>

                            <!-- Goal Unit -->
                            <div>
                                <label for="goal_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Goal Unit:</label>
                                <input type="text" name="goal_unit" id="goal_unit" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Category Ranges -->
                    <div class="overflow-x-auto mb-6 max-w-3xl mx-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Min</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Max</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                                @foreach(['Failed', 'Below Expectation', 'Threshold', 'Meet Target', 'Excellence'] as $index => $category)
                                    <tr class="{{ $index % 2 ? 'bg-gray-50 dark:bg-gray-600' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            <label for="category_{{$index+1}}_min">{{ $category }}:</label>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.01" 
                                                   name="category_{{$index+1}}_min" 
                                                   id="category_{{$index+1}}_min" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                   value="{{ old('category_'.($index+1).'_min') }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.01" 
                                                   name="category_{{$index+1}}_max" 
                                                   id="category_{{$index+1}}_max" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                   required 
                                                   value="{{ old('category_'.($index+1).'_max') }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('admin.kpi.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>Save Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
