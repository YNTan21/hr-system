@section('site-title', 'Create Goal')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2 class="text-2xl font-bold mb-4 text-center">Create Goal</h2> 
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Position:</label>
                    <input type="text" value="{{ $positions->position_name }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 cursor-not-allowed" readonly disabled>
                </div>

                <!-- Goal Creation Form -->
                <form action="{{ route('admin.kpi.store', ['position_id' => $positions->id]) }}" method="POST">
                    @csrf

                    <!-- Goal Name -->
                    <div class="form-group">
                        <label for="goal_name" class="block text-sm font-bold text-gray-700 mb-2">Goal Name</label>
                        <input type="text" name="goal_name" id="goal_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <!-- Goal Score -->
                    <div class="form-group">
                        <label for="goal_score" class="block text-sm font-bold text-gray-700 mb-2 mt-3">Goal Score</label>
                        <input type="number" name="goal_score" id="goal_score" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <!-- Goal Unit -->
                    <div class="form-group">
                        <label for="goal_unit" class="block text-sm font-bold text-gray-700 mb-2 mt-3">Goal Unit</label>
                        <input type="text" name="goal_unit" id="goal_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <!-- Goal Type (Monthly or Yearly) -->
                    <div class="form-group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 mt-3">Goal Type</label>

                        <div class="grid grid-cols-2 gap-4 max-w-md">
                            <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700">
                                <input id="goal_type_monthly" type="radio" value="monthly" name="goal_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" required>
                                <label for="goal_type_monthly" class="w-full py-2 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Monthly</label>
                            </div>
                            
                            <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700">
                                <input id="goal_type_yearly" type="radio" value="yearly" name="goal_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="goal_type_yearly" class="w-full py-2 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Yearly</label>
                            </div>
                        </div>
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
                                <td><label for="category_1_min">Failed :</label></td>
                                <td>
                                    <input type="number" name="category_1_min" id="category_1_min" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('category_1_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_1_max" id="category_1_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('category_1_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_2_min">Below Expectation :</label></td>
                                <td>
                                    <input type="number" name="category_2_min" id="category_2_min" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('category_2_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_2_max" id="category_2_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('category_2_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_3_min">Threshold :</label></td>
                                <td>
                                    <input type="number" name="category_3_min" id="category_3_min" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('category_3_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_3_max" id="category_3_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('category_3_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_4_min">Meet Target :</label></td>
                                <td>
                                    <input type="number" name="category_4_min" id="category_4_min" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('category_4_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_4_max" id="category_4_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('category_4_max') }}">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="category_5_min">Excellence :</label></td>
                                <td>
                                    <input type="number" name="category_5_min" id="category_5_min" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('category_5_min') }}">
                                </td>
                                <td>
                                    <input type="number" name="category_5_max" id="category_5_max" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('category_5_max') }}">
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
