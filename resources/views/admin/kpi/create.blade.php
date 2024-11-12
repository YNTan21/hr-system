@section('site-title', 'Create Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2>Create Goal for Position: {{ $positions->position_name }}</h2>

                <!-- Goal Creation Form -->
                <form action="{{ route('admin.kpi.store', ['position_id' => $positions->id]) }}" method="POST">
                    @csrf

                    <!-- Goal Name -->
                    <div class="form-group">
                        <label for="goal_name">Goal Name</label>
                        <input type="text" name="goal_name" id="goal_name" class="form-control" required>
                    </div>

                    <!-- Goal Score -->
                    <div class="form-group">
                        <label for="goal_score">Goal Score</label>
                        <input type="number" name="goal_score" id="goal_score" class="form-control" required>
                    </div>

                    <!-- Goal Unit -->
                    <div class="form-group">
                        <label for="goal_unit">Goal Unit</label>
                        <input type="text" name="goal_unit" id="goal_unit" class="form-control" required>
                    </div>

                    <!-- Goal Type (Monthly or Yearly) -->
                    <div class="form-group">
                        <label>Goal Type</label><br>
                        <input type="radio" name="goal_type" id="goal_type_monthly" value="monthly" required>
                        <label for="goal_type_monthly">Monthly</label><br>
                        
                        <input type="radio" name="goal_type" id="goal_type_yearly" value="yearly" required>
                        <label for="goal_type_yearly">Yearly</label>
                    </div>

                    {{-- Category Ranges --}}
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Min</th>
                                <th>Max</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="category_1_min">Category 1:</label></td>
                                <td>
                                    <input type="number" name="category_1_min" id="category_1_min" class="form-control" value="{{ old('category_1_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_1_max" id="category_1_max" class="form-control" required value="{{ old('category_1_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_2_min">Category 2:</label></td>
                                <td>
                                    <input type="number" name="category_2_min" id="category_2_min" class="form-control" value="{{ old('category_2_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_2_max" id="category_2_max" class="form-control" required value="{{ old('category_2_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_3_min">Category 3:</label></td>
                                <td>
                                    <input type="number" name="category_3_min" id="category_3_min" class="form-control" value="{{ old('category_3_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_3_max" id="category_3_max" class="form-control" required value="{{ old('category_3_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_4_min">Category 4:</label></td>
                                <td>
                                    <input type="number" name="category_4_min" id="category_4_min" class="form-control" value="{{ old('category_4_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_4_max" id="category_4_max" class="form-control" required value="{{ old('category_4_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_5_min">Category 5:</label></td>
                                <td>
                                    <input type="number" name="category_5_min" id="category_5_min" class="form-control" value="{{ old('category_5_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_5_max" id="category_5_max" class="form-control" required value="{{ old('category_5_max') }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-2">Save Goal</button>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
