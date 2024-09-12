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

                <form action="{{route('user.leave.store')}}" method="post">
                    @csrf
                    <div class="col px-5 pb-2">
                        <h3 class="title text-center">
                            Apply for Leave
                        </h3>
                    </div>

                    {{-- User name --}}
                    <div class="form-group">
                        <label for="user_id">Employee Name</label>
                        <input type="text" id="user_id" class="rounded-md border-gray-300 shadow-sm form-control bg-gray-200" value="{{ auth()->user()->username }}" readonly>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        @error('user_id')
                            <p class="error">
                                {{ $message }}
                            </p>
                        @enderror 
                    </div>

                    {{-- Leave Type dropdown --}}
                    <div class="form-group">
                        <label for="leave_type_id">Leave Type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-control" required>
                            <option value="" disabled selected>Select a leave type</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}">{{ $leaveType->leave_type }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <p class="error">
                                {{ $message }}
                            </p>
                        @enderror 
                    </div>

                    <div class="flex space-x-4">
                        <!-- From Date -->
                        <div class="flex-1">
                            <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                            @error('from_date')
                                <p class="error">
                                    {{ $message }}
                                </p>
                            @enderror 
                        </div>
                    
                        <!-- To Date -->
                        <div class="flex-1">
                            <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                            @error('to_date')
                                <p class="error">
                                    {{ $message }}
                                </p>
                            @enderror 
                        </div>
                    </div>
                    
                    <!-- Number of Days (Auto-calculated based on date selection, can be handled via JavaScript) -->
                    <div class="form-group">
                        <label for="number_of_days">Number of Days</label>
                        <input type="number" name="number_of_days" id="number_of_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" readonly>
                    </div>

                    <!-- Reason Text Area -->
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" required></textarea>
                        @error('reason')
                            <p class="error">
                                {{ $message }}
                            </p>
                        @enderror 
                    </div>

                    <!-- Add submit button -->
                    <div class="col text-center p-2 px-5">
                        <button type="submit" class="btn btn-dark">Submit</button>
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

