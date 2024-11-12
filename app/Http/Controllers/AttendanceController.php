<?php

namespace App\Http\Controllers;

use DateTime;
// use App\User;
use App\Latetime;
// use App\Attendance;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AttendanceEmp;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendance.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     return view('admin.attendance')->with(['attendances'=> Attendance::all()]);
    // }

    public function index()
    {
        $attendances = Attendance::paginate(10);
        return view('admin.attendance.index', compact('attendances'));
    }

    /**
     * Display a listing of the latetime.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexLatetime()
    {
        return view('admin.latetime')->with(['latetimes' => Latetime::all()]);
    }



    /**
     * assign attendance to employee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign(AttendanceEmp $request)
    {
        $request->validated();

        if ($employee = User::whereEmail(request('email'))->first()){

            if (Hash::check($request->pin_code, $employee->pin_code)) {
                    if (!Attendance::whereAttendance_date(date("Y-m-d"))->whereUser_id($employee->id)->first()){
                        $attendance = new Attendance;
                        $attendance->user_id = $employee->id;
                        $attendance->attendance_time = date("H:i:s");
                        $attendance->attendance_date = date("Y-m-d");

                        if (!($employee->schedules->first()->time_in >= $attendance->attendance_time)){
                            $attendance->status = 0;
                        AttendanceController::lateTime($employee);
                        };
                        $attendance->save();

                    }else{
                        return redirect()->route('attendance.login')->with('error', 'you assigned your attendance before');
                    }
                } else {
                return redirect()->route('attendance.login')->with('error', 'Failed to assign the attendance');
            }
        }



        return redirect()->route('home')->with('success', 'Successful in assign the attendance');
    }

    /**
     * assign late time for attendace .
     *
     * @return \Illuminate\Http\Response
     */
    public static function lateTime(User $employee)
    {
        $current_t= new DateTime(date("H:i:s"));
        $start_t= new DateTime($employee->schedules->first()->time_in);
        $difference = $start_t->diff($current_t)->format('%H:%I:%S');


        $latetime = new Latetime;
        $latetime->user_id = $employee->id;
        $latetime->duration = $difference;
        $latetime->latetime_date = date("Y-m-d");
        $latetime->save();

    }

    public function create()
    {
        $employees = Employee::all(); // Adjust based on your Employee model
        return view('admin.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|in:clock_in,clock_out',
        ]);

        $datetime = $request->date . ' ' . $request->time;

        Attendance::create([
            'employee_id' => $request->employee_id,
            'datetime' => $datetime,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance recorded successfully');
    }

}