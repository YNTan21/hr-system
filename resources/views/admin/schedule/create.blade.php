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
                        <!-- Shift Date -->
                        <div class="col-span-3">
                            <label for="shift_date" class="block text-sm font-medium text-gray-700">Shift Date</label>
                            <input type="date" 
                                   name="shift_date" 
                                   id="shift_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   value="{{ request('date', now('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
                                   required>
                        </div>

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

                        <!-- Hidden Start Time and End Time -->
                        <input type="hidden" name="start_time" id="start_time" value="00:00">
                        <input type="hidden" name="end_time" id="end_time" value="23:59">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('#user_ids').select2({
        placeholder: 'Select employees',
        allowClear: true
    });

    // 获取本地日期和时间
    const today = new Date();
    const localDate = today.toISOString().split('T')[0];
    const localTime = today.toLocaleTimeString('en-US', { 
        hour12: false,
        hour: '2-digit',
        minute: '2-digit'
    });

    // 设置日期字段
    const dateInput = document.getElementById('shift_date');
    if (dateInput && !dateInput.value) {
        dateInput.value = localDate;
    }

    // 获取时间输入字段
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const submitButton = document.getElementById('submit-button');

    // 设置时间值
    if (startTimeInput) startTimeInput.value = localTime;
    if (endTimeInput) endTimeInput.value = localTime;

    // 验证时间
    function validateTimes() {
        if (startTimeInput.value && endTimeInput.value) {
            if (startTimeInput.value >= endTimeInput.value) {
                endTimeInput.setCustomValidity('End time must be after start time');
                submitButton.disabled = true;
            } else {
                endTimeInput.setCustomValidity('');
                submitButton.disabled = false;
            }
        }
    }

    // 添加时间验证监听器
    if (startTimeInput) startTimeInput.addEventListener('change', validateTimes);
    if (endTimeInput) endTimeInput.addEventListener('change', validateTimes);

    // 添加 shift_code 改变事件监听
    document.getElementById('shift_code').addEventListener('change', function() {
        const shiftTimes = {
            'M': ['08:45', '18:15'],  // M: 8.45am-6.15pm
            'A': ['12:00', '21:00'],  // A: 12.00pm - 9.00pm
            'M1': ['09:30', '12:30'], // M1: 9.30am-12.30pm
            'F': ['08:45', '21:00'],  // F: 8.45am - 9.00pm
            'A2': ['18:00', '21:00'], // A2: 6.00pm-9.00pm
            'RD': ['00:00', '23:59'], // Rest Day
            'TR': ['09:00', '18:00'], // Training
            'AL': ['00:00', '23:59'], // Annual Leave
            'PH': ['00:00', '23:59']  // Public Holiday
        };

        const selectedShift = this.value;
        const times = shiftTimes[selectedShift] || ['00:00', '23:59'];

        if (startTimeInput) startTimeInput.value = times[0];
        if (endTimeInput) endTimeInput.value = times[1];
    });
});
</script>

