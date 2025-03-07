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
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $monthName }} {{ $currentYear }}
                        </h2>
                        <div class="flex items-center space-x-4">
                            <!-- Month Selector -->
                            <select onchange="window.location.href = '{{ route('admin.leave.calendar') }}?month=' + this.value + '&year={{ $currentYear }}'"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @foreach($months as $month)
                                    <option value="{{ $month['value'] }}" {{ $currentMonth == $month['value'] ? 'selected' : '' }}>
                                        {{ $month['name'] }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Year Selector -->
                            <select onchange="window.location.href = '{{ route('admin.leave.calendar') }}?month={{ $currentMonth }}&year=' + this.value"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Navigation Arrows -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.leave.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" 
                                   class="p-2 hover:bg-gray-100 rounded-lg dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.leave.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                                   class="p-2 hover:bg-gray-100 rounded-lg dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-7 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <!-- Days of Week -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                            <div class="bg-gray-50 dark:bg-gray-800 py-2 text-center text-sm font-medium text-gray-800 dark:text-gray-200 border-b border-r border-gray-200 dark:border-gray-700">
                                {{ $dayName }}
                            </div>
                        @endforeach

                        <!-- Calendar Days -->
                        @foreach($calendar as $day)
                            <div class="min-h-[120px] bg-white dark:bg-gray-800 p-2 relative border-b border-r border-gray-200 dark:border-gray-700 
                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}
                                {{ isset($day['holiday']) ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium 
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
                                    <div class="mt-1 space-y-1 overflow-y-auto max-h-[80px]">
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
</x-layout.master>