<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Timetable;
use App\Models\Shift;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['user', 'shift']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $timetables = $query->latest('date')->paginate(30);
        $users = User::all();
        $shifts = Shift::all();

        return view('admin.timetable.index', compact('timetables', 'users', 'shifts'));
    }
}