@section('site-title', 'Create Leave')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800 dark:text-white">Create Leave</h2>
                </div>

                <!-- Leave Creation Form -->
                <form action="{{ route('user.leave.store') }}" method="POST" class="max-w-3xl mx-auto">
                    @csrf

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Leave Type -->
                        <div>
                            <label for="leave_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Leave Type:</label>
                            <select name="leave_type_id" id="leave_type_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300" 
                                    required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->leave_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range and Number of Days -->
                        <div class="grid grid-cols-3 gap-4">
                            <!-- From Date -->
                            <div>
                                <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date:</label>
                                <input type="date" name="from_date" id="from_date" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                       value="{{ old('from_date') }}"
                                       min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                                       required>
                            </div>

                            <!-- To Date -->
                            <div>
                                <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date:</label>
                                <input type="date" name="to_date" id="to_date" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                       value="{{ old('to_date') }}"
                                       min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                                       required>
                            </div>

                            <!-- Number of Days -->
                            <div>
                                <label for="number_of_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Days:</label>
                                <input type="number" name="number_of_days" id="number_of_days" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                       readonly>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason:</label>
                            <textarea name="reason" id="reason" rows="2" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                      required>{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('user.leave.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>Save Leave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
    // Auto-calculate number of days based on 'from_date' and 'to_date'
    document.getElementById('from_date').addEventListener('change', calculateDays);
    document.getElementById('to_date').addEventListener('change', calculateDays);

    function calculateDays() {
        var fromDate = new Date(document.getElementById('from_date').value);
        var toDate = new Date(document.getElementById('to_date').value);

        if (fromDate && toDate && toDate >= fromDate) {
            var timeDifference = toDate.getTime() - fromDate.getTime();
            var daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24)) + 1; // Include the start day
            document.getElementById('number_of_days').value = daysDifference;
        } else {
            document.getElementById('number_of_days').value = '';
        }
    }

    // Validate dates
    document.getElementById('from_date').addEventListener('change', validateDates);
    document.getElementById('to_date').addEventListener('change', validateDates);

    function validateDates() {
        var fromDate = new Date(document.getElementById('from_date').value);
        var toDate = new Date(document.getElementById('to_date').value);

        if (fromDate && toDate && toDate < fromDate) {
            alert('To Date must be after From Date.');
            document.getElementById('to_date').value = '';
            document.getElementById('number_of_days').value = '';
        }
    }
</script>

