@section('site-title', 'Edit Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2>Edit Goal for Position: {{ $positions->position_name }}</h2>

                <!-- Goal Edit Form -->
                <form action="{{ route('admin.kpi.update', ['goal_id' => $goal->id, 'position_id' => $positions->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Goal Name -->
                    <div class="form-group">
                        <label for="goal_name">Goal Name</label>
                        <input type="text" name="goal_name" id="goal_name" class="form-control" value="{{ old('goal_name', $goal->goal_name) }}" required>
                    </div>

                    <!-- Goal Score -->
                    <div class="form-group">
                        <label for="goal_score">Goal Score</label>
                        <input type="number" name="goal_score" id="goal_score" class="form-control" value="{{ old('goal_score', $goal->goal_score) }}" required>
                    </div>

                    <!-- Goal Unit -->
                    <div class="form-group">
                        <label for="goal_unit">Goal Unit</label>
                        <input type="text" name="goal_unit" id="goal_unit" class="form-control" value="{{ old('goal_unit', $goal->goal_unit) }}" required>
                    </div>

                    <!-- Goal Type (Monthly or Yearly) -->
                    <div class="form-group">
                        <label>Goal Type</label><br>
                        <input type="radio" name="goal_type" id="goal_type_monthly" value="monthly" {{ $goal->goal_type == 'monthly' ? 'checked' : '' }} required>
                        <label for="goal_type_monthly">Monthly</label><br>
                        
                        <input type="radio" name="goal_type" id="goal_type_yearly" value="yearly" {{ $goal->goal_type == 'yearly' ? 'checked' : '' }} required>
                        <label for="goal_type_yearly">Yearly</label>
                    </div>

                    {{-- Category Ranges --}}
                    @php
                        $categoryScoreRanges = json_decode($goal->category_score_ranges, true);
                    @endphp
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Min</th>
                                <th>Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (range(1, 5) as $category)
                                <tr>
                                    <td><label for="category_{{ $category }}_min">Category {{ $category }}:</label></td>
                                    <td>
                                        <input type="number" name="category_{{ $category }}_min" id="category_{{ $category }}_min" class="form-control" value="{{ old('category_' . $category . '_min', $categoryScoreRanges['category_' . $category]['min'] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" name="category_{{ $category }}_max" id="category_{{ $category }}_max" class="form-control" value="{{ old('category_' . $category . '_max', $categoryScoreRanges['category_' . $category]['max'] ?? '') }}" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-2">Save Goal</button>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
