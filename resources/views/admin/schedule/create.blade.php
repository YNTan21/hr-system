@section('site-title', 'Create Schedule')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                {{-- Messages --}}
                @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.schedule.store') }}" method="POST">
                    @csrf
                    <div class="col px-5 pb-2">
                        <h3 class="title text-center">
                            Create Schedule
                        </h3>
                    </div>

                    <!-- Multiple Employee Selection -->
                    <div class="mb-4">
                        <label for="user_ids" class="block text-sm font-medium text-gray-700">Select Employees</label>
                        <div class="mt-1 w-full rounded-md border border-gray-300 shadow-sm">
                            @foreach($employees as $employee)
                                <div class="flex items-center p-2 hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="user_ids[]" 
                                           id="user_{{ $employee->id }}"
                                           value="{{ $employee->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="user_{{ $employee->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $employee->username }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-1">You can select multiple employees by checking the boxes</p>
                    </div>

                    <!-- Schedule Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Shift Selection -->
                        <div class="col-span-3">
                            <label for="shift_code" class="block text-sm font-medium text-gray-700">Select Shift</label>
                            <select name="shift_code" 
                                    id="shift_code" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                    required>
                                <option value="">Select a shift</option>
                                <option value="M">M: 8.45am-6.15pm Rest 1.5H</option>
                                <option value="A">A: 12.00pm - 9.00pm</option>
                                <option value="M1">M1: 9.30am-12.30pm</option>
                                <option value="F">F: 8.45am - 9.00pm</option>
                                <option value="A2">A2: 6.00pm-9.00pm/5.45pm-9.00pm</option>
                                <option value="RD">RD: Rest Day</option>
                                <option value="TR">TR: Training</option>
                                <option value="AL">AL: Annual Leave</option>
                                <option value="PH">PH: Public Holiday</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col text-center p-2 px-5 mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors disabled:opacity-50" id="submit-button">
                            Create Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('#user_ids').select2({
        placeholder: 'Select employees',
        allowClear: true
    });

    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const submitButton = document.getElementById('submit-button');

    function validateTimes() {
        if (startTime.value && endTime.value) {
            if (startTime.value >= endTime.value) {
                endTime.setCustomValidity('End time must be after start time');
                submitButton.disabled = true;
            } else {
                endTime.setCustomValidity('');
                submitButton.disabled = false;
            }
        }
    }

    startTime.addEventListener('change', validateTimes);
    endTime.addEventListener('change', validateTimes);
});
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />

