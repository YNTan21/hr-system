@section('site-title', 'Manage KPI')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2>Manage KPI for Position: {{ $positions->position_name }}</h2>

                @if ($goals->isEmpty())
                    <p>No goals assigned for this position. Please add a goal first.</p>
                    <a href="{{ route('admin.kpi.create', ['position_id' => $positions->id]) }}" class="btn btn-primary">Add Goal</a>
                @else
                    <!-- Show goals table if goals exist -->
                    <div class="mt-3">
                        <h4>Existing Goals</h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Goal Name</th>
                                    <th class="text-center">Goal Type</th>
                                    <th class="text-center">Goal Unit</th>
                                    <th class="text-center">Goal Score</th>
                                    <th class="text-center">Failed</th>
                                    <th class="text-center">Below Expectation</th>
                                    <th class="text-center">Threshold</th>
                                    <th class="text-center">Meet Target</th>
                                    <th class="text-center">Excellence</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($goals as $index => $goal)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $goal->goal_name }}</td>
                                        <td class="text-center">{{ ucfirst($goal->goal_type) }}</td>
                                        <td class="text-center">{{ $goal->goal_unit }}</td>
                                        <td class="text-center">{{ $goal->goal_score }}</td>
                        
                                        <!-- Category Score Ranges -->
                                        @php
                                            $categoryScoreRanges = json_decode($goal->category_score_ranges, true);
                                        @endphp
                        
                                        <td class="text-center">
                                            {{ $categoryScoreRanges['category_1']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_1']['max'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $categoryScoreRanges['category_2']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_2']['max'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $categoryScoreRanges['category_3']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_3']['max'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $categoryScoreRanges['category_4']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_4']['max'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $categoryScoreRanges['category_5']['min'] ?? 'N/A' }} - {{ $categoryScoreRanges['category_5']['max'] ?? 'N/A' }}
                                        </td>
                        
                                        <td class="text-center">
                                            <a href="{{ route('admin.kpi.edit', ['goal_id' => $goal->id]) }}" class="btn btn-sm bg-yellow-100 text-yellow-500">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.kpi.destroy', ['goal_id' => $goal->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-red-100 text-red-500" onclick="return confirm('Are you sure you want to delete this employee?')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Button to add new goal -->
                        <a href="{{ route('admin.kpi.create', ['position_id' => $positions->id]) }}" class="btn btn-info">Add Goal</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-layout.master>
