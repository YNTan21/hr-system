@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                {{-- Success Message --}}
                @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Employee Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date and Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <!-- Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="clock_in">Clock In</option>
                                <option value="clock_out">Clock Out</option>
                            </select>
                        </div>

                        <div class="my-10"></div>

                        <!-- Button Row -->
                        <div class="flex justify-between items-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Attendance
                            </button>
                            
                            <a href="{{ route('admin.attendance.index') }}" class="text-gray-600 hover:text-gray-800">
                                <button type="button" class="flex items-center text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
                
                </div>
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
</script>

<script>
    document.getElementById('from_date').addEventListener('change', validateDates);
    document.getElementById('to_date').addEventListener('change', validateDates);

    function validateDates() {
        var fromDate = new Date(document.getElementById('from_date').value);
        var toDate = new Date(document.getElementById('to_date').value);

        if (fromDate && toDate && toDate < fromDate) {
            alert('To Date must be after From Date.');
            document.getElementById('to_date').value = '';
        }
    }
</script>