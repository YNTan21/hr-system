@section('site-title', 'Manage KPI')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Manage KPI for Position: {{ $positions->position_name }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.kpi.create', ['position_id' => $positions->id]) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-plus"></i> Add Goal
                        </a>
                        <a href="{{ route('admin.kpi.manage.export', ['position_id' => $positions->id]) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                @if ($goals->isEmpty())
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 text-sm">
                        <p>No goals assigned for this position. Please add a goal first.</p>
                    </div>
                @else
                    <!-- Goals Table -->
                    <div class="overflow-x-auto relative">
                        <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                            <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                                <tr>
                                    <th class="py-3 px-3">#</th>
                                    <th class="py-3 px-3">Goal Name</th>
                                    <th class="py-3 px-3">Unit</th>
                                    <th class="py-3 px-3">Score</th>
                                    <th class="py-3 px-3">Failed</th>
                                    <th class="py-3 px-3">Below Expectation</th>
                                    <th class="py-3 px-3">Threshold</th>
                                    <th class="py-3 px-3">Meet Target</th>
                                    <th class="py-3 px-3">Excellence</th>
                                    <th class="py-3 px-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @php
                                    $totalScore = 0;
                                @endphp
                                @foreach ($goals as $index => $goal)
                                    @php
                                        $totalScore += $goal->goal_score;
                                        $categoryScoreRanges = json_decode($goal->category_score_ranges, true);
                                    @endphp
                                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-2.5 px-3">{{ $index + 1 }}</td>
                                        <td class="py-2.5 px-3">{{ $goal->goal_name }}</td>
                                        <td class="py-2.5 px-3">{{ $goal->goal_unit }}</td>
                                        <td class="py-2.5 px-3">{{ $goal->goal_score }}</td>
                                        <td class="py-2.5 px-3">{{ $categoryScoreRanges['category_1']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_1']['max'] ?? 'N/A' }}</td>
                                        <td class="py-2.5 px-3">{{ $categoryScoreRanges['category_2']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_2']['max'] ?? 'N/A' }}</td>
                                        <td class="py-2.5 px-3">{{ $categoryScoreRanges['category_3']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_3']['max'] ?? 'N/A' }}</td>
                                        <td class="py-2.5 px-3">{{ $categoryScoreRanges['category_4']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_4']['max'] ?? 'N/A' }}</td>
                                        <td class="py-2.5 px-3">{{ $categoryScoreRanges['category_5']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_5']['max'] ?? 'N/A' }}</td>
                                        <td class="py-2.5 px-3">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.kpi.edit', ['position_id' => $positions->id, 'id' => $goal->id]) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.kpi.destroy', ['goal_id' => $goal->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this goal?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Total Score Row -->
                                <tr class="bg-gray-50 font-bold">
                                    <td colspan="3" class="py-3 px-3 text-right">Total Score:</td>
                                    <td class="py-3 px-3">{{ number_format($totalScore, 2) }}</td>
                                    <td colspan="6"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout.master>
