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
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Leave</h2>
                </div>
                @if (session('success'))
                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 text-center" role="alert">
                    {{ session('success') }}
                </div>
                @endif
                @if ($errors->has('annual_leave_balance'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 text-center" role="alert">
                    {{ $errors->first('annual_leave_balance') }}
                </div>
                @endif
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <form action="{{ route('user.leave.store') }}" method="POST">
                    @csrf
                    <!-- Row: From Date, To Date, Number of Days -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <!-- From Date -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                                </div>
                                <label for="from_date" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">From</label>
                            </div>
                            <div class="relative flex-1">
                                <input type="text" id="from_date" name="from_date" class="flex-1 pl-10 rounded-md border-2 border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" placeholder="Select date" autocomplete="off" required value="{{ old('from_date') }}">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            </div>
                            @error('from_date')
                                <p class="ml-4 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- To Date -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-check text-white text-sm"></i>
                                </div>
                                <label for="to_date" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">To</label>
                            </div>
                            <div class="relative flex-1">
                                <input type="text" id="to_date" name="to_date" class="flex-1 pl-10 rounded-md border-2 border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" placeholder="Select date" autocomplete="off" required value="{{ old('to_date') }}">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            </div>
                            @error('to_date')
                                <p class="ml-4 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                            <!-- Number of Days -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-hourglass-half text-white text-sm"></i>
                                </div>
                                <label for="number_of_days" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Days</label>
                            </div>
                            <input type="number" name="number_of_days" id="number_of_days" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" readonly value="{{ old('number_of_days') }}">
                        </div>
                    </div>
                    <!-- Row: Leave Type and Reason -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Leave Type Dropdown -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-tags text-white text-sm"></i>
                                </div>
                                <label for="leave_type_id" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Leave Type</label>
                            </div>
                            <div class="flex-1 relative overflow-visible">
                                <button id="dropdownLeaveTypeButton" data-dropdown-toggle="dropdownLeaveType" type="button" class="flex-1 bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span id="selectedLeaveType">
                                        {{ $leaveTypes->firstWhere('id', old('leave_type_id', $selectedLeaveType))?->leave_type ?? 'Select a leave type' }}
                                    </span>
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                </button>
                                <div id="dropdownLeaveType" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                    <ul class="py-2 text-sm text-gray-700 max-h-48 overflow-y-auto" aria-labelledby="dropdownLeaveTypeButton">
                                        @foreach ($leaveTypes as $leaveType)
                                        <li>
                                            <a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="{{ $leaveType->id }}">{{ $leaveType->leave_type }}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" name="leave_type_id" id="leave_type_id" required value="{{ old('leave_type_id', $selectedLeaveType) }}">
                                @error('leave_type_id')
                                    <p class="ml-4 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Reason -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-align-left text-white text-sm"></i>
                                </div>
                                <label for="reason" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Reason</label>
                            </div>
                            <textarea name="reason" id="reason" rows="2" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" required>{{ old('reason') }}</textarea>
                        @error('reason')
                                <p class="ml-4 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('user.leave.index') }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-save mr-2"></i>Save Leave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
<script>
    // Flatpickr for date fields
    flatpickr("#from_date", {
        minDate: "today",
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('from_date') }}",
        onChange: function(selectedDates, dateStr, instance) {
            toPicker.set('minDate', dateStr);
            calculateDays();
        }
    });
    var toPicker = flatpickr("#to_date", {
        minDate: "today",
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('to_date') }}",
        onChange: function(selectedDates, dateStr, instance) {
            calculateDays();
        }
    });
    function calculateDays() {
        var fromDateStr = document.getElementById('from_date').value;
        var toDateStr = document.getElementById('to_date').value;
        if (fromDateStr && toDateStr) {
            var fromDate = new Date(fromDateStr);
            var toDate = new Date(toDateStr);
            if (!isNaN(fromDate) && !isNaN(toDate) && toDate >= fromDate) {
            var timeDifference = toDate.getTime() - fromDate.getTime();
                var daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24)) + 1;
            document.getElementById('number_of_days').value = daysDifference;
            } else {
                document.getElementById('number_of_days').value = '';
            }
        } else {
            document.getElementById('number_of_days').value = '';
        }
    }
    // Custom dropdown for Leave Type
    document.getElementById('dropdownLeaveTypeButton').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('dropdownLeaveType').classList.toggle('hidden');
    });
    document.querySelectorAll('#dropdownLeaveType a[data-value]').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var value = this.getAttribute('data-value');
            var text = this.textContent;
            document.getElementById('leave_type_id').value = value;
            document.getElementById('selectedLeaveType').textContent = text;
            document.getElementById('dropdownLeaveType').classList.add('hidden');
        });
    });
    // Set initial selected leave type
    document.addEventListener('DOMContentLoaded', function() {
        var leaveTypeId = document.getElementById('leave_type_id').value;
        if (leaveTypeId) {
            var selected = document.querySelector('#dropdownLeaveType a[data-value="' + leaveTypeId + '"]');
            if (selected) {
                document.getElementById('selectedLeaveType').textContent = selected.textContent;
            }
        }
        calculateDays();
    });
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('dropdownLeaveType');
        var button = document.getElementById('dropdownLeaveTypeButton');
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>

