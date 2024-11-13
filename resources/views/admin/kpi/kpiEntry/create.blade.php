@section('site-title', 'Create KPI Entry')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>Create KPI Entry - {{ $goal->goal_name }}</h2>
                
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.kpi.kpiEntry.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="users_id" value="{{ $user_id }}">
                            <input type="hidden" name="goals_id" value="{{ $goal->id }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">

                            <div class="mb-3">
                                <label class="form-label">Goal Name</label>
                                <input type="text" class="form-control" value="{{ $goal->goal_name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Goal Score</label>
                                <input type="text" class="form-control" value="{{ $goal->goal_score }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category Ranges</label>
                                @php
                                    $ranges = json_decode($goal->category_score_ranges, true);
                                @endphp
                                @foreach($ranges as $category => $range)
                                    <div>
                                        {{ ucfirst(str_replace('_', ' ', $category)) }}: 
                                        {{ $range['min'] }} - {{ $range['max'] }}
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label for="actual_result" class="form-label">Actual Result *</label>
                                <input type="number" step="0.01" class="form-control @error('actual_result') is-invalid @enderror" 
                                    id="actual_result" name="actual_result" required>
                                @error('actual_result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Entry</button>
                                <a href="{{ route('admin.kpi.kpiEntry.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.master> 