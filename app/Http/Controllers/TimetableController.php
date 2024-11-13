<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TimetableController extends Controller
{
    public function index()
    {
        $employees = User::all();
        return view('admin.timetable.index', compact('employees'));
    }
}