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
    <section class="content-header">
        <h1>
            Attendance
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Attendance</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        {{-- @include('includes.messages') --}}

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <a href="/attendance/assign" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New Attendance</a>
                    </div>
                    <div class="box-body">
                        <table id="attendanceTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Attendance Status</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ date('Y-m-d', strtotime($attendance->attendance_date)) }}</td>
                                        <td>{{ $attendance->user_id }}</td>
                                        <td>{{ $attendance->user->name }}</td>
                                        <td>
                                            {{ $attendance->attendance_time }}
                                            @if($attendance->status == 1)
                                                <span class="label label-warning pull-right">On Time</span>
                                            @else
                                                <span class="label label-danger pull-right">Late</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->user->schedules->first()->time_in }}</td>
                                        <td>{{ $attendance->user->schedules->first()->time_out }}</td>
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
    </section>
</div>
</div>
</div>
</div>
</x-layout.master>