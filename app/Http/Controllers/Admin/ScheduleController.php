<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // ... 你现有的代码 ...
        
        // 添加这行来查看数据
        dd($schedules->first());  // 这会显示第一条记录的所有数据
    }
}