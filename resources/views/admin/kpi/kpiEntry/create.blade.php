@section('site-title', 'Create KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Create KPI Entry</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $goal->goal_name }}</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form method="POST" action="{{ route('admin.kpi.kpiEntry.store') }}">
                        @csrf
                        <input type="hidden" name="goals_id" value="{{ $goal->id }}">
                        <input type="hidden" name="users_id" value="{{ $user->id }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <!-- Goal Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Goal Name</label>
                                <input type="text" class="bg-gray-50 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       value="{{ $goal->goal_name }}" disabled>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Goal Score</label>
                                <input type="text" class="bg-gray-50 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       value="{{ $goal->goal_score }}" disabled>
                            </div>
                        </div>

                        <!-- Category Ranges -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category Ranges</label>
                            <div class="bg-gray-50 rounded-md p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @php
                                    $ranges = json_decode($goal->category_score_ranges, true);
                                    $categoryLabels = [
                                        'category_1' => 'Failed (0)',
                                        'category_2' => 'Below Expectation (1)',
                                        'category_3' => 'Threshold (2)',
                                        'category_4' => 'Meet Target (3)',
                                        'category_5' => 'Excellence (4)'
                                    ];
                                @endphp
                                @foreach($ranges as $category => $range)
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700">{{ $categoryLabels[$category] }}:</span>
                                        <span class="text-gray-600">{{ $range['min'] }} - {{ $range['max'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Actual Result Input -->
                        <div class="mb-6">
                            <label for="actual_result" class="block text-sm font-medium text-gray-700 mb-2">
                                Actual Result <span class="text-red-600">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('actual_result') border-red-500 @enderror" 
                                   id="actual_result" 
                                   name="actual_result" 
                                   value="{{ old('actual_result') }}"
                                   required>
                            @error('actual_result')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.kpi.kpiEntry.index', [
                                'user_id' => $user->id,
                                'month' => $month,
                                'year' => $year
                            ]) }}" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.master> 