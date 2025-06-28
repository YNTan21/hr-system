@section('site-title', 'Edit KPI Entry')
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
                    <h2 class="text-2xl font-bold text-gray-800">Edit KPI Entry</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $entry->goal->goal_name }}</p>
                </div>

                <!-- KPI Entry Form -->
                <form action="{{ route('user.kpi.update', $entry->id) }}" method="POST" class="max-w-3xl mx-auto">
                    @csrf
                    @method('PUT')

                    <!-- Goal Details Card -->
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-bullseye text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Goal Details</h3>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-star text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">Goal Score:</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $entry->goal->goal_score }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center mb-3">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-chart-line text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Category Ranges</span>
                                </div>
                                
                                <div class="flex justify-between gap-1">
                                    @php
                                        $ranges = json_decode($entry->goal->category_score_ranges, true);
                                        $categoryNames = [
                                            'category_1' => 'Failed (0)',
                                            'category_2' => 'Below Expectation (1)',
                                            'category_3' => 'Threshold (2)',
                                            'category_4' => 'Meet Target (3)',
                                            'category_5' => 'Excellence (4)'
                                        ];
                                    @endphp
                                    @foreach($ranges as $category => $range)
                                        <div class="bg-gray-50 px-2 py-2 rounded-lg border border-gray-200 text-xs flex-1 text-center">
                                            <div class="flex items-center justify-center">
                                                <div class="w-4 h-4 rounded-full bg-blue-100 border border-blue-300 flex items-center justify-center mr-1">
                                                    <span class="text-xs font-bold text-blue-600">{{ array_search($category, array_keys($ranges)) + 1 }}</span>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $categoryNames[$category] }}</div>
                                                    <div class="text-gray-600">{{ $range['min'] }}-{{ $range['max'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actual Result Card -->
                    <div class="mb-6">
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-bar text-white text-sm"></i>
                                </div>
                                <label for="actual_result" class="text-sm font-semibold text-gray-700">
                                    Actual Result <span class="text-red-600">*</span>
                                </label>
                            </div>
                            <input type="number" 
                                   name="actual_result" 
                                   id="actual_result" 
                                   step="0.01"
                                   value="{{ $entry->actual_result }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                   placeholder="Enter actual result"
                                   required>
                            @error('actual_result')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Simple Action Buttons -->
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('user.kpi.manage', ['month' => $entry->month, 'year' => $entry->year]) }}" 
                           class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>Update Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master> 