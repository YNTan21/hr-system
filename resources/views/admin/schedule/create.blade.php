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
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 text-center">Create Schedule</h2>
                        <p class="text-gray-600 text-center mt-2">Create new schedule for employees</p>
                    </div>

                    <form action="{{ route('admin.schedule.store') }}" method="POST">
                        @csrf
                        
                        <!-- Multiple Employee Selection -->
                        <div class="mb-6">
                            <label for="user_ids" class="block text-sm font-semibold text-gray-700 mb-3">
                                Select Employees <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 max-h-60 overflow-y-auto rounded-md border border-gray-300 shadow-sm px-2">
                                @foreach($employees as $employee)
                                    <div class="flex items-center py-2 hover:bg-gray-50 transition duration-150">
                                        <input type="checkbox" 
                                               name="user_ids[]" 
                                               id="user_{{ $employee->id }}"
                                               value="{{ $employee->id }}"
                                               class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="user_{{ $employee->id }}" class="ml-2.5 block text-sm text-gray-700 cursor-pointer">
                                            {{ $employee->username }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select multiple employees by checking the boxes
                            </p>
                        </div>

                        <!-- Schedule Details -->
                        <div class="space-y-6">
                            <!-- Shift Date -->
                            <div>
                                <label for="shift_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Shift Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="shift_date" 
                                       id="shift_date" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                       value="{{ request('date', now('Asia/Kuala_Lumpur')->format('Y-m-d')) }}"
                                       required>
                            </div>

                            <!-- Shift Selection -->
                            <div>
                                <label for="shift_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Select Shift <span class="text-red-500">*</span>
                                </label>
                                <select name="shift_code" 
                                        id="shift_code" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white" 
                                        required>
                                    <option value="" class="text-gray-500">Select a shift</option>
                                    <optgroup label="Regular Shifts" class="font-semibold text-gray-700">
                                        <option value="M" class="text-gray-700 py-2">M: 8.45am-6.15pm Rest 1.5H</option>
                                        <option value="A" class="text-gray-700 py-2">A: 12.00pm - 9.00pm</option>
                                        <option value="M1" class="text-gray-700 py-2">M1: 9.30am-12.30pm</option>
                                        <option value="F" class="text-gray-700 py-2">F: 8.45am - 9.00pm</option>
                                        <option value="A2" class="text-gray-700 py-2">A2: 6.00pm-9.00pm/5.45pm-9.00pm</option>
                                    </optgroup>
                                    <optgroup label="Special Cases" class="font-semibold text-gray-700 mt-2">
                                        <option value="RD" class="text-gray-700 py-2">RD: Rest Day</option>
                                        <option value="TR" class="text-gray-700 py-2">TR: Training</option>
                                        <option value="AL" class="text-gray-700 py-2">AL: Annual Leave</option>
                                        <option value="PH" class="text-gray-700 py-2">PH: Public Holiday</option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Hidden Start Time and End Time -->
                            <input type="hidden" name="start_time" id="start_time" value="00:00">
                            <input type="hidden" name="end_time" id="end_time" value="23:59">
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center gap-4 mt-8">
                            <a href="{{ route('admin.schedule.index') }}" 
                               class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                    id="submit-button">
                                <i class="fas fa-save mr-2"></i> Create Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

<style>
    select option {
        padding: 8px;
        margin: 2px 0;
    }
    
    select optgroup {
        padding: 6px;
        background-color: #f3f4f6;
    }
    
    select option:hover {
        background-color: #e5e7eb;
    }
    
    select {
        padding: 8px 12px;
    }
</style>

