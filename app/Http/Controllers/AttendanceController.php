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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendance.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        
        $attendances = Attendance::with('user')
            ->when($request->user_id, function($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->date, function($query) use ($request) {
                return $query->whereDate('date', $request->date);
            })
            ->latest('date')
            ->paginate(10);
        
        return view('admin.attendance.index', compact('users', 'attendances'));
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
        $users = User::where('status', 'active')
            ->orderBy('username')
            ->get();
        
        return view('admin.attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in_time' => 'nullable',
            'clock_out_time' => 'nullable',
            'status' => 'required|in:on_time,late'
        ]);

        try {
            Attendance::create([
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
                'clock_in_time' => $validated['clock_in_time'],
                'clock_out_time' => $validated['clock_out_time'],
                'status' => $validated['status']
            ]);

            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance recorded successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to save attendance. ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        return Excel::download(new AttendanceExport, 'attendance.xlsx');
    }

}