@section('site-title', 'Add KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Add KPI Entry - {{ $goal->goal_name }}</h2>
                    <a href="{{ route('user.kpi.manage', ['month' => $month, 'year' => $year]) }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <!-- KPI Entry Form -->
                <form action="{{ route('user.kpi.store') }}" method="POST" class="max-w-3xl mx-auto">
                    @csrf
                    <input type="hidden" name="goals_id" value="{{ $goal->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <!-- Goal Details -->
                    <div class="mb-6">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="font-semibold mb-2">Goal Details:</h3>
                            <p><span class="font-medium">Goal Score:</span> {{ $goal->goal_score }}</p>
                            <div class="mt-2">
                                <span class="font-medium">Category Ranges:</span>
                                @php
                                    $ranges = json_decode($goal->category_score_ranges, true);
                                    $categoryNames = [
                                        'category_1' => 'Failed (0)',
                                        'category_2' => 'Below Expectation (1)',
                                        'category_3' => 'Threshold (2)',
                                        'category_4' => 'Meet Target (3)',
                                        'category_5' => 'Excellence (4)'
                                    ];
                                @endphp
                                <ul class="list-disc list-inside ml-4 mt-1">
                                    @foreach($ranges as $category => $range)
                                        <li class="text-sm">
                                            <span class="font-medium">{{ $categoryNames[$category] }}:</span>
                                            {{ $range['min'] }} - {{ $range['max'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Actual Result -->
                    <div class="mb-6">
                        <label for="actual_result" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Actual Result:
                        </label>
                        <input type="number" 
                               name="actual_result" 
                               id="actual_result" 
                               step="0.01"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               required>
                        @error('actual_result')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center gap-4">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-save mr-2"></i>Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master> 