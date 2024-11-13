<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="sidebar">
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="" role="button" aria-expanded="false" aria-controls="collapseExample">
            Dashboard
        </a>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="" role="button" aria-expanded="false" aria-controls="collapseExample">
            Employee
        </a>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="{{ route('admin.attendance.index') }}" role="button" aria-expanded="false" aria-controls="collapseExample">
            Attendance
        </a>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            Leave Type <i class="fa-solid fa-angle-down"></i>
        </a>
        <div class="collapse" id="collapseExample">
            <div class="">
                {{-- card card-body --}}
                <a href="{{route('leaveType.create')}}" class="sidebar-submenu-link text-dark text-decoration-none">
                    <a type="button" class="btn btn-light">New Leave Type</a>
                    {{-- <i class="fa-solid fa-circle-plus pe-1"></i>New Leave --}}
                </a>
            </div>
            <div class="">
                {{-- card card-body --}}
                <a href="" class="sidebar-submenu-link text-dark text-decoration-none">
                    <a type="button" class="btn btn-light">Manage Leave</a>
                    {{-- <i class="fa-solid fa-circle-plus pe-1"></i>New Leave --}}
                </a>
            </div>
        </div>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="" role="button" aria-expanded="false" aria-controls="collapseExample">
            Appraisal
        </a>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="{{ route('admin.timetable.index') }}" role="button" aria-expanded="false" aria-controls="collapseExample">
            Timetable
        </a>
    </div>
    <div class="col p-2">
        <a class="sidebar-mainmenu-link text-dark text-decoration-none" data-bs-toggle="collapse" href="" role="button" aria-expanded="false" aria-controls="collapseExample">
            Calender
        </a>
    </div>
</div>