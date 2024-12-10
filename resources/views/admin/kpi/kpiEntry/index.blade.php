@section('site-title', 'KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>KPI Entry Management - {{ $months[$currentMonth] }} {{ $currentYear }}</h2>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.kpi.kpiEntry.index') }}" class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- User Filter -->
                        <div class="flex-1">
                            <select name="user_id" id="user_id" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUser->id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="flex-1">
                            <select name="month" id="month" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="flex-1">
                            <select name="year" id="year" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">View Month</button>
                    </div>
                </form>

                <!-- Add this section at the top of your content area for debugging -->
                {{-- @if(config('app.debug'))
                    <div class="bg-gray-100 p-4 mb-4">
                        <h4>Debug Information:</h4>
                        <p>User ID: {{ $selectedUser->id }}</p>
                        <p>Position ID: {{ $selectedUser->position_id }}</p>
                        <p>Number of Goals: {{ $goals->count() }}</p>
                        <p>Goals:</p>
                        <ul>
                            @foreach($goals as $goal)
                                <li>{{ $goal->goal_name }} (ID: {{ $goal->id }}, Position: {{ $goal->position_id }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <!-- KPI Table -->
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Goal Name</th>
                            <th>Goal Score</th>
                            <th>Category Ranges</th>
                            <th>Actual Result</th>
                            <th>Actual Score</th>
                            <th>Final Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($goals as $index => $goal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $goal->goal_name }}</td>
                                <td>{{ $goal->goal_score }}</td>
                                <td>
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
                                    @if($ranges)
                                        @foreach($ranges as $category => $range)
                                            {{ $categoryNames[$category] }}: 
                                            {{ $range['min'] }} - {{ $range['max'] }}<br>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $existingEntries[$goal->id]->actual_result ?? '-' }}</td>
                                <td>{{ $existingEntries[$goal->id]->actual_score ?? '-' }}</td>
                                <td>{{ $existingEntries[$goal->id]->final_score ?? '-' }}</td>
                                <td>
                                    @if(isset($existingEntries[$goal->id]))
                                        <a href="{{ route('admin.kpi.kpiEntry.edit', [
                                            'id' => $existingEntries[$goal->id]->id,
                                            'month' => $currentMonth,
                                            'year' => $currentYear
                                        ]) }}" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                    @else
                                        <a href="{{ route('admin.kpi.kpiEntry.create', [
                                            'goal_id' => $goal->id,
                                            'month' => $currentMonth,
                                            'year' => $currentYear,
                                            'user_id' => $selectedUser->id
                                        ]) }}" class="btn btn-primary btn-sm">
                                            Add Result
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    No goals found for position ID: {{ $selectedUser->position_id }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
