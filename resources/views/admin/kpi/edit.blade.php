@section('site-title', 'Edit Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Simple Header -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Edit Goal</h2>
                </div>

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

                    <!-- Simple Position Card -->
                    <div class="mb-4">
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-tie text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-semibold text-blue-800">Position</h3>
                                    <p class="text-blue-600 text-sm">{{ $position->position_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Goal Details in Simple Cards -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <!-- Goal Name Card -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-bullseye text-white text-sm"></i>
                                </div>
                                <label for="goal_name" class="text-sm font-semibold text-gray-700">Goal Name</label>
                            </div>
                            <input type="text" name="goal_name" id="goal_name" 
                                   value="{{ old('goal_name', $goal->goal_name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Enter goal name" required>
                        </div>

                        <!-- Goal Score Card -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-star text-white text-sm"></i>
                                </div>
                                <label for="goal_score" class="text-sm font-semibold text-gray-700">Goal Score</label>
                            </div>
                            <input type="number" step="0.01" name="goal_score" id="goal_score" 
                                   value="{{ old('goal_score', $goal->goal_score) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="0.00" required>
                        </div>

                        <!-- Goal Unit Card -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-ruler text-white text-sm"></i>
                                </div>
                                <label for="goal_unit" class="text-sm font-semibold text-gray-700">Goal Unit</label>
                            </div>
                            <input type="text" name="goal_unit" id="goal_unit" 
                                   value="{{ old('goal_unit', $goal->goal_unit) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="e.g., %, units, hours" required>
                        </div>
                    </div>

                    <!-- Simple Category Ranges Table -->
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-line text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Category Score Ranges</h3>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-white border-b-2 border-gray-200">
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 bg-blue-50">Category</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 bg-blue-50">Min Value</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 bg-blue-50">Max Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        @foreach(['Failed', 'Below Expectation', 'Threshold', 'Meet Target', 'Excellence'] as $index => $category)
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="w-6 h-6 rounded-full bg-blue-100 border-2 border-blue-300 flex items-center justify-center mr-3">
                                                            <span class="text-xs font-bold text-blue-600">{{ $index + 1 }}</span>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-700">{{ $category }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" step="0.01" 
                                                           name="category_{{$index+1}}_min" 
                                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                                           placeholder="Min" 
                                                           value="{{ old('category_' . ($index + 1) . '_min', $goal->category_score_ranges['category_' . ($index + 1)]['min'] ?? '') }}">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" step="0.01" 
                                                           name="category_{{$index+1}}_max" 
                                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                                           placeholder="Max" 
                                                           required 
                                                           value="{{ old('category_' . ($index + 1) . '_max', $goal->category_score_ranges['category_' . ($index + 1)]['max'] ?? '') }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Simple Action Buttons -->
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('admin.kpi.index') }}" 
                           class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>Update Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
