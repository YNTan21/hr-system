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
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <!-- Employee Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" 
                                   name="date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   required>
                        </div>

                        <!-- Clock In Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clock In Time</label>
                            <input type="time" 
                                   name="clock_in_time" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('clock_in_time') }}">
                        </div>

                        <!-- Clock Out Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clock Out Time</label>
                            <input type="time" 
                                   name="clock_out_time" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('clock_out_time') }}">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="on_time">On Time</option>
                                <option value="late">Late</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-between mt-4">
                            <a href="{{ route('admin.attendance.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Attendance
                            </button>
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

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif