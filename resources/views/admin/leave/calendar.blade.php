@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64 mt-14">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <!-- Calendar Header -->
                <div class="p-3 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $monthName }} {{ $currentYear }}
                            </h2>
                            <a href="{{ route('admin.leave.calendar', ['month' => now()->month, 'year' => now()->year]) }}" 
                               class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30 transition-all duration-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Today
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Month Selector -->
                            <div class="relative">
                                <button id="monthDropdownButton" data-dropdown-toggle="monthDropdown" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-blue-800 w-36" type="button">
                                    {{ $months->firstWhere('value', $currentMonth)['name'] ?? 'Select Month' }}
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>

                                <!-- Month Dropdown menu -->
                                <div id="monthDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-36 dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="monthDropdownButton">
                                        @foreach($months as $month)
                                            <li>
                                                <a href="{{ route('admin.leave.calendar') }}?month={{ $month['value'] }}&year={{ $currentYear }}" 
                                                   class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentMonth == $month['value'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                    {{ $month['name'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Year Selector -->
                            <div class="relative">
                                <button id="yearDropdownButton" data-dropdown-toggle="yearDropdown" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-blue-800 w-32" type="button">
                                    {{ $currentYear }}
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>

                                <!-- Year Dropdown menu -->
                                <div id="yearDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-32 dark:bg-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200 max-h-52 overflow-y-auto" aria-labelledby="yearDropdownButton">
                                        @foreach($years as $year)
                                            <li>
                                                <a href="{{ route('admin.leave.calendar') }}?month={{ $currentMonth }}&year={{ $year }}" 
                                                   class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentYear == $year ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                    {{ $year }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('admin.leave.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" 
                                   class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.leave.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                                   class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="p-2">
                    <div class="grid grid-cols-7 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden max-h-[80vh] overflow-y-auto">
                        <!-- Days of Week -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                            <div class="bg-gray-50 dark:bg-gray-800 py-1 text-center text-xs font-medium text-gray-800 dark:text-gray-200 border-b border-r border-gray-200 dark:border-gray-700">
                                {{ $dayName }}
                            </div>
                        @endforeach

                        <!-- Calendar Days -->
                        @foreach($calendar as $day)
                            <div class="calendar-day min-h-[64px] bg-white dark:bg-gray-800 p-1 relative border-b border-r border-gray-200 dark:border-gray-700 
                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}
                                {{ isset($day['holiday']) ? 'bg-red-50 dark:bg-red-900/10' : '' }} transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium 
                                        {{ $day['isCurrentMonth'] ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }} 
                                        {{ $day['isToday'] ? 'bg-blue-100 dark:bg-blue-900 px-2 py-0.5 rounded-full' : '' }}">
                                        {{ $day['day'] }}
                                    </span>
                                </div>
                                
                                <!-- Public Holiday -->
                                @if(isset($day['holiday']))
                                    <div class="mt-1">
                                        <div class="text-xs px-2 py-1 rounded bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 font-medium">
                                            {{ $day['holiday']['name'] }}
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Leave Events -->
                                @if(isset($day['leaves']) && count($day['leaves']) > 0)
                                    <div class="mt-1 space-y-1 overflow-y-auto max-h-[40px]">
                                        @foreach($day['leaves'] as $leave)
                                            <div class="text-xs px-2 py-1 rounded"
                                                 style="background-color: {{ $leave->leaveType->color }}15; color: {{ $leave->leaveType->color }};">
                                                {{ $leave->user->username }} - {{ $leave->leaveType->code }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Leave Type Legend -->
                <div class="p-6 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Leave Types</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($leaveTypes as $type)
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $type->color }};"></div>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $type->code }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">- {{ $type->leave_type }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Calendar JavaScript -->
    <script>
        // Add hover effects for calendar days
        document.addEventListener('DOMContentLoaded', function() {
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach(day => {
                day.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                day.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
    
    <!-- Include Flowbite JS for dropdowns -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</x-layout.master>