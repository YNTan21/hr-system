@section('site-title', 'KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">KPI Entry Management - {{ $months[$currentMonth] }} {{ $currentYear }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.kpi.kpiEntry.export', ['user_id' => $selectedUser->id, 'month' => $currentMonth, 'year' => $currentYear]) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.kpi.kpiEntry.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- User Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUser->id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Month</label>
                            <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex space-x-2">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.kpi.kpiEntry.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- KPI Table -->
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="py-3 px-3">#</th>
                                <th class="py-3 px-3 max-w-xs">Goal Name</th>
                                <th class="py-3 px-3">Goal Score</th>
                                <th class="py-3 px-3">Category Ranges</th>
                                <th class="py-3 px-3">Actual Result</th>
                                <th class="py-3 px-3">Actual Score</th>
                                <th class="py-3 px-3">Final Score</th>
                                <th class="py-3 px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $totalFinalScore = 0;
                                $totalGoalScore = 0;
                            @endphp
                            @forelse($goals as $index => $goal)
                                @php
                                    $totalGoalScore += $goal->goal_score;
                                    if(isset($existingEntries[$goal->id])) {
                                        $totalFinalScore += $existingEntries[$goal->id]->final_score;
                                    }
                                @endphp
                                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                    <td class="py-2.5 px-3">{{ $index + 1 }}</td>
                                    <td class="py-2.5 px-3 max-w-xs">
                                        <div class="truncate text-left" title="{{ $goal->goal_name }}">
                                            {{ $goal->goal_name }}
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3">{{ $goal->goal_score }}</td>
                                    <td class="py-2.5 px-3 text-left">
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
                                        @foreach($ranges as $category => $range)
                                            <div class="text-xs leading-4">
                                                <span class="font-semibold">{{ $categoryNames[$category] }}:</span>
                                                {{ $range['min'] }} - {{ $range['max'] }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $existingEntries[$goal->id]->actual_result ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $existingEntries[$goal->id]->actual_score ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $existingEntries[$goal->id]->final_score ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex justify-center space-x-2">
                                            @if(isset($existingEntries[$goal->id]))
                                                <a href="{{ route('admin.kpi.kpiEntry.edit', [
                                                    'id' => $existingEntries[$goal->id]->id,
                                                    'month' => $currentMonth,
                                                    'year' => $currentYear
                                                ]) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.kpi.kpiEntry.destroy', $existingEntries[$goal->id]->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.kpi.kpiEntry.create', [
                                                    'goal_id' => $goal->id,
                                                    'month' => $currentMonth,
                                                    'year' => $currentYear,
                                                    'user_id' => $selectedUser->id
                                                ]) }}" 
                                                   class="text-blue-600 hover:text-blue-900"
                                                   title="Add Entry">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-2.5 px-3 text-center">
                                        No goals found for this position
                                    </td>
                                </tr>
                            @endforelse
                            <!-- Total Row -->
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="2" class="py-3 px-3 text-right">Total</td>
                                <td class="py-3 px-3">{{ $totalGoalScore }}</td>
                                <td class="py-3 px-3"></td>
                                <td class="py-3 px-3"></td>
                                <td class="py-3 px-3"></td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $totalFinalScore }}
                                    </span>
                                </td>
                                <td class="py-3 px-3"></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Add Total Percentage -->
                    <div class="mt-4 text-right">
                        <p class="text-sm font-medium">
                            Total Achievement: 
                            <span class="text-blue-600">
                                {{ $totalGoalScore > 0 ? round(($totalFinalScore / $totalGoalScore) * 100, 2) : 0 }}%
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="kpiEntryModal" tabindex="-1">
        <!-- Modal content here -->
    </div>
</x-layout.master>

@push('scripts')
<script>
function addEntry(goalId) {
    // Implement modal open logic for adding new entry
}

function editEntry(entryId) {
    // Implement modal open logic for editing existing entry
}
</script>
@endpush
