@section('site-title', 'Attendance Records')

<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>Attendance Records</h2>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Filter Date Form -->
                <form method="GET" action="{{ route('admin.attendance-schedule.index') }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filter_date" class="form-label">Select Date</label>
                            <input type="date" class="form-control" name="filter_date" id="filter_date" value="{{ request('filter_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary mt-4">Filter</button>
                        </div>
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Shift</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->user->username }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                            <td>
                                @if($schedule->attendanceSchedules->where('date', $date)->first())
                                    {{ \Carbon\Carbon::parse($schedule->attendanceSchedules->where('date', $date)->first()->clock_in)->format('Y-m-d H:i:s') }}
                                @endif
                            </td>
                            <td>
                                @if($schedule->attendanceSchedules->where('date', $date)->first())
                                    {{ \Carbon\Carbon::parse($schedule->attendanceSchedules->where('date', $date)->first()->clock_out)->format('Y-m-d H:i:s') }}
                                @endif
                            </td>
                            <td>
                                @if($schedule->attendanceSchedules->where('date', $date)->first())
                                    {{ $schedule->attendanceSchedules->where('date', $date)->first()->status }}
                                @endif
                            </td>
                            <td>
                                <!-- Button to trigger modal -->
                                <button class="btn {{ $schedule->attendanceSchedules->where('date', $date)->first() ? 'btn-warning' : 'btn-primary' }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#addAttendanceModal-{{ $schedule->id }}">
                                    {{ $schedule->attendanceSchedules->where('date', $date)->first() ? 'Edit Attendance' : 'Add Attendance' }}
                                </button>
                    
                                <!-- Modal -->
                                <div class="modal fade" id="addAttendanceModal-{{ $schedule->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.attendance-schedule.store') }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addAttendanceModalLabel">
                                                        {{ $schedule->attendanceSchedules->where('date', $date)->first() ? 'Edit Attendance' : 'Add Attendance' }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @php
                                                        $existingAttendance = $schedule->attendanceSchedules->where('date', $date)->first();
                                                    @endphp

                                                    <!-- Hidden fields -->
                                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                    <input type="hidden" name="user_id" value="{{ $schedule->user_id }}">
                                                    <input type="hidden" name="date" value="{{ $date }}">

                                                    <!-- Display Employee and Schedule -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Employee</label>
                                                        <input type="text" class="form-control" value="{{ $schedule->user->username }}" readonly>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Schedule Time</label>
                                                        <input type="text" class="form-control" 
                                                               value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                                      {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" readonly>
                                                    </div>

                                                    <!-- Clock In / Clock Out Fields -->
                                                    <div class="mb-3">
                                                        <label for="clock_in" class="form-label">Clock In</label>
                                                        <input type="datetime-local" 
                                                               class="form-control @error('clock_in') is-invalid @enderror" 
                                                               name="clock_in" 
                                                               id="clock_in"
                                                               value="{{ $existingAttendance && $existingAttendance->clock_in ? \Carbon\Carbon::parse($existingAttendance->clock_in)->format('Y-m-d\TH:i') : '' }}">
                                                        @error('clock_in')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="clock_out" class="form-label">Clock Out</label>
                                                        <input type="datetime-local" 
                                                               class="form-control @error('clock_out') is-invalid @enderror" 
                                                               name="clock_out" 
                                                               id="clock_out"
                                                               value="{{ $existingAttendance && $existingAttendance->clock_out ? \Carbon\Carbon::parse($existingAttendance->clock_out)->format('Y-m-d\TH:i') : '' }}">
                                                        @error('clock_out')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ $existingAttendance ? 'Update Attendance' : 'Save Attendance' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                                  
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript to validate clock_out > clock_in -->
    <script>
        document.querySelectorAll('[id^="attendance-form-"]').forEach(form => {
            form.addEventListener('submit', function(event) {
                const clockIn = document.getElementById('clock_in').value;
                const clockOut = document.getElementById('clock_out').value;

                if (new Date(clockOut) <= new Date(clockIn)) {
                    event.preventDefault();
                    alert('Clock Out time must be later than Clock In time.');
                }
            });
        });
    </script>

</x-layout.master>
