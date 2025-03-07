@section('site-title', 'Combined Calendar')
<x-layout.master>
    <div class="container-fluid">
        <div class="row border-b shadow-lg fixed top-0 right-0 left-0 bg-white dark:bg-gray-800 z-10">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                @php
                    $shiftColors = [
                        'M' => [
                            'bg' => 'bg-blue-100 dark:bg-blue-900/20',
                            'text' => 'text-blue-800 dark:text-blue-200'
                        ],
                        'A' => [
                            'bg' => 'bg-green-100 dark:bg-green-900/20',
                            'text' => 'text-green-800 dark:text-green-200'
                        ],
                        'M1' => [
                            'bg' => 'bg-purple-100 dark:bg-purple-900/20',
                            'text' => 'text-purple-800 dark:text-purple-200'
                        ],
                        'F' => [
                            'bg' => 'bg-yellow-100 dark:bg-yellow-900/20',
                            'text' => 'text-yellow-800 dark:text-yellow-200'
                        ],
                        'A2' => [
                            'bg' => 'bg-pink-100 dark:bg-pink-900/20',
                            'text' => 'text-pink-800 dark:text-pink-200'
                        ],
                        'RD' => [
                            'bg' => 'bg-gray-100 dark:bg-gray-900/20',
                            'text' => 'text-gray-800 dark:text-gray-200'
                        ],
                        'TR' => [
                            'bg' => 'bg-indigo-100 dark:bg-indigo-900/20',
                            'text' => 'text-indigo-800 dark:text-indigo-200'
                        ],
                        'PH' => [
                            'bg' => 'bg-red-100 dark:bg-red-900/20',
                            'text' => 'text-red-800 dark:text-red-200'
                        ],
                        'AL' => [
                            'bg' => 'bg-orange-100 dark:bg-orange-900/20',
                            'text' => 'text-orange-800 dark:text-orange-200'
                        ]
                    ];
                @endphp

                <!-- Calendar Header with Filters -->
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Month/Year Display -->
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="text-blue-600 dark:text-blue-400">{{ $monthName }}</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $currentYear }}</span>
                        </h2>
                        
                        <!-- Filters -->
                        <div class="flex items-center bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-1">
                            <!-- Previous Month Button -->
                            <a href="{{ route('admin.calendar.index', ['month' => $prevMonth, 'year' => $prevYear]) }}" 
                               class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>

                            <!-- Month Selector -->
                            <div class="mx-1">
                                <select id="month-selector" 
                                        class="w-32 bg-transparent border-0 text-gray-700 dark:text-gray-300 text-sm font-medium focus:ring-0 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded px-2 py-1">
                                    @foreach($months as $month)
                                        <option value="{{ $month['value'] }}" {{ $currentMonth == $month['value'] ? 'selected' : '' }}>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Year Selector -->
                            <div class="mx-1">
                                <select id="year-selector" 
                                        class="w-24 bg-transparent border-0 text-gray-700 dark:text-gray-300 text-sm font-medium focus:ring-0 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded px-2 py-1">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Next Month Button -->
                            <a href="{{ route('admin.calendar.index', ['month' => $nextMonth, 'year' => $nextYear]) }}" 
                               class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Today Button -->
                        <button onclick="goToToday()" 
                                class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-sm font-medium transition-colors dark:bg-blue-900/20 dark:hover:bg-blue-900/30 dark:text-blue-400">
                            Today
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                    <!-- Days of Week -->
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                        <div class="bg-gray-50 dark:bg-gray-800 px-2 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $dayName }}
                        </div>
                    @endforeach

                    <!-- Calendar Days -->
                    @foreach($calendar as $date => $day)
                        <div class="min-h-[120px] bg-white dark:bg-gray-800 p-2 relative 
                            {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                            <!-- Date Number -->
                            <div class="text-right mb-2">
                                <span class="{{ !$day['isCurrentMonth'] ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ $day['day'] }}
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-1">
                                <!-- Schedule Button - Moved to top -->
                                @if(count($day['schedules']) > 0)
                                    <button type="button" 
                                            class="w-full text-sm px-2 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded relative group font-medium">
                                        <div class="flex items-center justify-between">
                                            <span>Schedule</span>
                                            <span class="inline-flex items-center justify-center bg-purple-200 text-purple-700 rounded-full w-5 h-5 text-xs font-medium">
                                                {{ count($day['schedules']) }}
                                            </span>
                                        </div>
                                        <!-- Schedule Hover Details -->
                                        <div class="absolute left-full top-0 ml-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-3 hidden group-hover:block z-10">
                                            <div class="text-sm font-medium mb-2">Schedule Details</div>
                                            @foreach($day['schedules'] as $schedule)
                                                <div class="text-xs mb-2 p-2 rounded bg-purple-50 dark:bg-purple-900/20">
                                                    <div><span class="font-bold">{{ $schedule->user->username }}</span> - {{ $schedule->shift_code }}</div>
                                                    <div class="text-gray-500 mt-1">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </button>
                                @endif

                                <!-- Leave Button - Moved to bottom -->
                                @if(count($day['leaves']) > 0)
                                    <button type="button" 
                                            class="w-full text-sm px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded relative group font-medium">
                                        <div class="flex items-center justify-between">
                                            <span>Leave</span>
                                            <span class="inline-flex items-center justify-center bg-blue-200 text-blue-700 rounded-full w-5 h-5 text-xs font-medium">
                                                {{ count($day['leaves']) }}
                                            </span>
                                        </div>
                                        <!-- Leave Hover Details -->
                                        <div class="absolute left-full top-0 ml-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-3 hidden group-hover:block z-10">
                                            <div class="text-sm font-medium mb-2">Leave Details</div>
                                            @foreach($day['leaves'] as $leave)
                                                <div class="text-xs mb-2 p-2 rounded" 
                                                     style="background-color: {{ $leave->leaveType->color }}15; color: {{ $leave->leaveType->color }};">
                                                    <div><span class="font-bold">{{ $leave->user->username }}</span> - {{ $leave->leaveType->leave_type }}</div>
                                                    <div class="text-gray-500 mt-1">{{ $leave->from_date }} - {{ $leave->to_date }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="p-6 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Schedule Types Legend -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Schedule Types</h3>
                            <div class="space-y-2">
                                <!-- Regular Shifts -->
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        M
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">8:45am - 6:15pm (Rest 1.5H)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        A
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">12:00pm - 9:00pm</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        M1
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">9:30am - 12:30pm</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        F
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">8:45am - 9:00pm</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        A2
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">6:00pm - 9:00pm / 5:45pm - 9:00pm</span>
                                </div>
                                <!-- Special Cases -->
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        RD
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Rest Day</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        TR
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Training</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                        PH
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Public Holiday</span>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Types Legend -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Leave Types</h3>
                            <div class="space-y-2">
                                @foreach($leaveTypes as $type)
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-12 px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-center">
                                            {{ $type->code }}
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $type->leave_type }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Month/Year Selection -->
    <script>
        function goToToday() {
            const today = new Date();
            const month = today.getMonth() + 1; // JavaScript months are 0-based
            const year = today.getFullYear();
            window.location.href = `{{ route('admin.calendar.index') }}?month=${month}&year=${year}`;
        }

        document.getElementById('month-selector').addEventListener('change', function() {
            updateCalendar();
        });

        document.getElementById('year-selector').addEventListener('change', function() {
            updateCalendar();
        });

        function updateCalendar() {
            const month = document.getElementById('month-selector').value;
            const year = document.getElementById('year-selector').value;
            window.location.href = `{{ route('admin.calendar.index') }}?month=${month}&year=${year}`;
        }
    </script>
</x-layout.master> 
