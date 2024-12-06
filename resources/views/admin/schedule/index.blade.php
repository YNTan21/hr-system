@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
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
                        </button>
                        <span id="weekDisplay" class="text-lg font-medium"></span>
                        <button onclick="nextWeek()" class="px-3 py-2 bg-gray-200 rounded-lg">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.schedule.create') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Add Shift
                    </a>
                </div>

                {{-- <!-- Debug section - remove after fixing -->
                <div class="mb-4 p-4 bg-gray-100 rounded">
                    <p>Debug Info:</p>
                    <p>Current Week Start: {{ $currentWeekStart }}</p>
                    <p>Number of Schedules: {{ $schedules->count() }}</p>
                    <p>Number of Employees: {{ $employees->count() }}</p>
                    
                    @foreach($schedules as $schedule)
                    <div class="mt-2">
                        <p>Schedule: {{ $schedule->shift_date }} - 
                           User: {{ $schedule->user->username ?? 'No user' }} - 
                           Time: {{ $schedule->start_time }} to {{ $schedule->end_time }}</p>
                    </div>
                    @endforeach
                </div>
                <!-- End debug section --> --}}

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
                                        <td class="border px-4 py-2 relative group">
                                            <div class="text-center">
                                                @php
                                                    $date = (clone $currentWeekStart)->addDays($i);
                                                    $shifts = $schedules->filter(fn($s) =>
                                                        $s->user_id === $employee->id &&
                                                        \Carbon\Carbon::parse($s->shift_date)->format('Y-m-d') === $date->format('Y-m-d')
                                                    );
                                                @endphp
                        
                                                @if ($shifts->isNotEmpty())
                                                    @foreach ($shifts as $shift)
                                                        <div class="text-sm bg-blue-100 p-2 rounded mb-1">
                                                            <div class="font-medium text-blue-800">
                                                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                                                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                                            </div>
                                                            {{-- Uncomment to display notes --}}
                                                            {{-- <div class="text-xs text-blue-600">
                                                                {{ $shift->notes ?? 'Regular Shift' }}
                                                            </div> --}}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <button onclick="addShift({{ $employee->id }}, {{ $i }})" 
                                                            class="absolute inset-0 w-full h-full bg-gray-50/75 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                                        <i class="fas fa-plus-circle text-blue-600 hover:text-blue-800"></i>
                                                    </button>
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
let currentDate = new Date('{{ $currentWeekStart->format('Y-m-d') }}');
let currentWeekStart = getWeekStart(currentDate);

function getWeekStart(date) {
    const today = new Date(date);
    const day = today.getDay();
    const diff = today.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(today.setDate(diff));
}

function updateWeekDisplay() {
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    for(let i = 0; i < 7; i++) {
        const date = new Date(currentWeekStart);
        date.setDate(date.getDate() + i);
        
        document.getElementById(`date-${i}`).textContent = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        });
        document.getElementById(`day-${i}`).textContent = date.toLocaleDateString('en-US', { 
            weekday: 'short' 
        });
    }
    
    document.getElementById('weekDisplay').textContent = 
        `${currentWeekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
}

function previousWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() - 7);
    updateWeekDisplay();
    updateURL();
}

function nextWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() + 7);
    updateWeekDisplay();
    updateURL();
}

function updateURL() {
    const params = new URLSearchParams(window.location.search);
    params.set('week_start', currentWeekStart.toISOString().split('T')[0]);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

function addShift(employeeId, dayIndex) {
    const date = new Date(currentWeekStart);
    date.setDate(date.getDate() + dayIndex);
    window.location.href = `/admin/schedule/create?employee=${employeeId}&date=${date.toISOString().split('T')[0]}`;
}

// Initialize the display
updateWeekDisplay();
</script>
