@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- 添加成功消息提示 -->
                @if (session('success'))
                    <div class="alert alert-success text-center bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Weekly Schedule</h1>

                <!-- Button to Display Current Week Schedule -->
                <div class="text-center mb-4">
                    <a href="{{ route('admin.schedule.current') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                        Display Current Week Schedule
                    </a>
                </div>

                <!-- Button to Select Page -->
                <div class="text-center mb-4">
                    <a href="{{ route('admin.schedule.select') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                        Select Date
                    </a>
                </div>

                <!-- Week Filter -->
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <button onclick="previousWeek()" class="px-3 py-2 bg-gray-200 rounded-lg">
                            <i class="fas fa-chevron-left"></i>
                            <span class="fallback-arrow">&lt;</span>
                        </button>
                        <span id="weekDisplay" class="text-lg font-medium"></span>
                        <button onclick="nextWeek()" class="px-3 py-2 bg-gray-200 rounded-lg">
                            <i class="fas fa-chevron-right"></i>
                            <span class="fallback-arrow">&gt;</span>
                        </button>
                    </div>
                    <a href="{{ route('admin.schedule.create') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Add Shift
                    </a>
                </div>

                <!-- Schedule Table -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-100">Employee Name</th>
                                @for ($i = 0; $i < 7; $i++)
                                    <th class="px-4 py-2 bg-gray-100">
                                        <div class="text-center">
                                            <div class="font-medium" id="date-{{ $i }}"></div>
                                            <div class="text-sm text-gray-600" id="day-{{ $i }}"></div>
                                        </div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="border px-4 py-2">{{ $employee->username }}</td>
                                    @for ($i = 0; $i < 7; $i++)
                                        <td class="border px-4 py-2 relative group schedule-cell">
                                            <div class="text-center">
                                                @php
                                                    // 获取当前列的日期
                                                    $currentDate = $currentWeekStart->copy()->addDays($i);
                                                    $currentDateString = $currentDate->format('Y-m-d');
                                                    
                                                    // 根据具体日期和员工ID过滤排班
                                                    $shifts = $schedules->filter(function($schedule) use ($employee, $currentDateString) {
                                                        return $schedule->user_id === $employee->id && 
                                                               $schedule->shift_date->format('Y-m-d') === $currentDateString;
                                                    });
                                                @endphp

                                                @if ($shifts->isNotEmpty())
                                                    @foreach ($shifts as $shift)
                                                        <div class="text-sm bg-blue-100 p-2 rounded mb-1 schedule-item">
                                                            <div class="font-medium text-blue-800">
                                                                {{ $shift->shift_code }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="h-full min-h-[40px] relative">
                                                        <button onclick="addShift({{ $employee->id }}, '{{ $currentDateString }}')" 
                                                                class="absolute inset-0 w-full h-full flex items-center justify-center">
                                                            <span class="text-gray-300 hover:text-gray-600 text-xl transition-colors duration-200">+</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
let currentDate = new Date();
let currentWeekStart = new Date();

// Check for URL parameter
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const weekStartParam = params.get('week_start');
    if (weekStartParam) {
        currentWeekStart = new Date(weekStartParam);
    } else {
        currentWeekStart = getWeekStart(currentDate);
    }
    updateWeekDisplay();
});

function getWeekStart(date) {
    const newDate = new Date(date);
    return new Date(newDate.setHours(0, 0, 0, 0));
}

function updateWeekDisplay() {
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    // 确保所有元素都存在
    const weekDisplay = document.getElementById('weekDisplay');
    if (!weekDisplay) {
        console.error('Could not find weekDisplay element');
        return;
    }

    // 更新每一天的显示
    for(let i = 0; i < 7; i++) {
        const dateElement = document.getElementById(`date-${i}`);
        const dayElement = document.getElementById(`day-${i}`);
        
        if (!dateElement || !dayElement) {
            console.error(`Could not find elements for day ${i}`);
            continue;
        }

        const date = new Date(currentWeekStart);
        date.setDate(date.getDate() + i);
        
        dateElement.textContent = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        });
        dayElement.textContent = date.toLocaleDateString('en-US', { 
            weekday: 'short' 
        });
    }
    
    // 更新周显示
    weekDisplay.textContent = 
        `${currentWeekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
}

async function updateScheduleDisplay() {
    const params = new URLSearchParams(window.location.search);
    params.set('week_start', currentWeekStart.toISOString().split('T')[0]);

    // 添加加载状态
    document.querySelectorAll('.schedule-item').forEach(item => {
        item.classList.add('fade-out');
    });

    try {
        const response = await fetch(`${window.location.pathname}?${params.toString()}`);
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // 平滑更新表格内容
        const cells = document.querySelectorAll('.schedule-cell');
        const newCells = doc.querySelectorAll('.schedule-cell');
        
        cells.forEach((cell, index) => {
            setTimeout(() => {
                const newContent = newCells[index].innerHTML;
                cell.innerHTML = newContent;
                cell.querySelectorAll('.schedule-item').forEach(item => {
                    item.classList.add('fade-in');
                });
            }, index * 50); // 添加微小延迟使更新看起来更平滑
        });

        // 更新 URL 但不刷新页面
        history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
    } catch (error) {
        console.error('Failed to update schedule:', error);
    }
}

function previousWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() - 1);
    updateWeekDisplay();
    updateScheduleDisplay();
}

function nextWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() + 1);
    updateWeekDisplay();
    updateScheduleDisplay();
}

function addShift(employeeId, date) {
    window.location.href = `/admin/schedule/create?employee=${employeeId}&date=${date}`;
}
</script>

<style>
.fas { display: inline-block; }
.fas.fa-chevron-left:before { content: '\f053'; }
.fas.fa-chevron-right:before { content: '\f054'; }
.fallback-arrow { display: none; }
.fas:not(:before) + .fallback-arrow { display: inline-block; }

/* 添加过渡动画 */
.schedule-cell {
    transition: all 0.3s ease-in-out;
}

.schedule-item {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.3s ease-in-out;
}

.schedule-item.fade-out {
    opacity: 0;
    transform: translateY(-10px);
}

.schedule-item.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
