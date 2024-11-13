@section('site-title', 'Employee Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- <section class="content-header">
        <h1>
            Attendance
        </h1>
    </section> -->

    <!-- Filter Row -->
    <div class="mb-4 p-4">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex gap-4 items-end">
            <!-- Employee Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    <option value="">Select Employees</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Existing New Attendance Button -->
    <div class="text-right mb-2">
        <a href="{{ route('admin.attendance.create')}}">
            <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                <i class="fa fa-plus"></i> New Attendance
            </button>
        </a>
    </div>

    <!-- Main content -->
    <section class="content">
        @if($attendances->isEmpty())
            <div class="text-center p-4 text-gray-500">
                No attendance records found.
            </div>
        @else
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="attendanceTable" class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Date</th>
                                        <th scope="col" class="py-3 px-6">Employee ID</th>
                                        <th scope="col" class="py-3 px-6">Name</th>
                                        <th scope="col" class="py-3 px-6">Status</th>
                                        <th scope="col" class="py-3 px-6">Clock In</th>
                                        <th scope="col" class="py-3 px-6">Clock Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{ date('Y-m-d', strtotime($attendance->date)) }}</td>
                                            <td>{{ $attendance->user_id }}</td>
                                            <td>{{ $attendance->user->username }}</td>
                                            <td>
                                                @if($attendance->status == 'on_time')
                                                    <span class="label label-warning pull-right">On Time</span>
                                                @else
                                                    <span class="label label-danger pull-right">Late</span>
                                                @endif
                                            </td>
                                            <td>{{ $attendance->clock_in_time }}</td>
                                            <td>{{ $attendance->clock_out_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Optional: Add pagination if necessary -->
                        <div class="box-footer">
                            {{ $attendances->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- For debugging -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
            </div>
        @endif
    </section>
</div>
</div>
</div>
</div>
</x-layout.master>