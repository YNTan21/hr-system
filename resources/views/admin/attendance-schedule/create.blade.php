@section('site-title', 'Attendance Records')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">

                <h2>Add Attendance</h2>

    <form action="{{ route('admin.attendance-schedule.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="user_id" class="form-label">Employee</label>
            <select class="form-control" name="user_id" id="user_id">
                <option value="" disabled selected>Select Employee</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="schedule_id" class="form-label">Schedule</label>
            <select class="form-control" name="schedule_id" id="schedule_id">
                <option value="" disabled selected>Select Schedule</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}">{{ $schedule->start_time }} - {{ $schedule->end_time }} ({{ $schedule->user->name }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="clock_in" class="form-label">Clock In</label>
            <input type="datetime-local" class="form-control" name="clock_in" id="clock_in">
        </div>

        <div class="mb-3">
            <label for="clock_out" class="form-label">Clock Out</label>
            <input type="datetime-local" class="form-control" name="clock_out" id="clock_out">
        </div>

        <button type="submit" class="btn btn-primary">Save Attendance</button>
    </form>

            </div>
        </div>
    </div>
</x-layout.master>  
