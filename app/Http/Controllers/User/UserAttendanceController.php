<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::where('user_id', Auth::id())
            ->select('attendances.*')
            ->orderBy('date', 'desc')
            ->orderBy('clock_in_time', 'desc');

        // 日期筛选
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // 月份筛选
        if ($request->filled('month')) {
            $month = $request->month;
            $query->whereYear('date', substr($month, 0, 4))
                  ->whereMonth('date', substr($month, 5, 2));
        }

        $attendances = $query->paginate(10)
            ->withQueryString();

        return view('user.attendance.index', compact('attendances'));
    }
}
