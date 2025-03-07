@section('site-title', 'Schedule Calendar')
<x-layout.master>
    <div class="container-fluid">
        <div class="row border-b shadow-lg fixed top-0 right-0 left-0 bg-white dark:bg-gray-800 z-10">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                @if (session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Add this PHP code at the top of the file -->
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

                <!-- Calendar Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Month:</span>
                            <div class="relative">
                                <select id="monthSelect" onchange="updateFilter()" class="bg-gray-50 border-0 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 pl-3 pr-10 w-32 dark:bg-gray-700 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors appearance-none">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
                        <div class="flex items-center relative">
                            <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Year:</span>
                            <div class="relative">
                                <select id="yearSelect" onchange="updateFilter()" class="bg-gray-50 border-0 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 pl-3 pr-10 w-28 dark:bg-gray-700 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors appearance-none">
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = $currentYear - 2;
                                        $endYear = $currentYear + 2;
                                    @endphp
                                    @for($year = $startYear; $year <= $endYear; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.schedule.create') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Schedule</span>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-[0.5px] bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <!-- Calendar Headers -->
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="bg-gray-100 dark:bg-gray-800 p-2 text-center font-medium border-b border-gray-200 dark:border-gray-700">
                            {{ $day }}
                        </div>
                    @endforeach

                    <!-- Calendar Days -->
                    @foreach($calendar as $day)
                        <div class="min-h-[120px] bg-white dark:bg-gray-800 p-2 relative border border-gray-200/50 dark:border-gray-700/50 
                            {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium 
                                    {{ $day['isCurrentMonth'] ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }} 
                                    {{ $day['isToday'] ? 'bg-blue-100 dark:bg-blue-900 px-2 py-0.5 rounded-full' : '' }}">
                                    {{ $day['day'] }}
                                </span>
                            </div>

                            <!-- Schedules for the day -->
                            @if(isset($day['schedules']) && count($day['schedules']) > 0)
                                <div class="mt-1 space-y-1 overflow-y-auto max-h-[80px]">
                                    @foreach($day['schedules'] as $schedule)
                                        <div class="text-xs px-2 py-1 rounded flex items-center justify-between
                                            {{ $shiftColors[$schedule->shift_code]['bg'] ?? 'bg-gray-100 dark:bg-gray-900/20' }}
                                            {{ $shiftColors[$schedule->shift_code]['text'] ?? 'text-gray-800 dark:text-gray-200' }}">
                                            <span>{{ $schedule->user->username }}</span>
                                            <span class="font-medium">{{ $schedule->shift_code }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Add Schedule Link -->
                            @if($day['isCurrentMonth'])
                                <a href="{{ route('admin.schedule.create', ['date' => $day['date']]) }}" 
                                   class="absolute bottom-1 right-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="font-semibold mb-2">Shift Codes:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach([
                            'M' => '8:45am - 6:15pm',
                            'A' => '12:00pm - 9:00pm',
                            'M1' => '9:30am - 12:30pm',
                            'F' => '8:45am - 9:00pm',
                            'A2' => '6:00pm - 9:00pm',
                            'RD' => 'Rest Day',
                            'TR' => 'Training',
                            'PH' => 'Public Holiday',
                            'AL' => 'Annual Leave'
                        ] as $code => $time)
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-6 h-6 rounded 
                                    {{ $shiftColors[$code]['bg'] ?? 'bg-gray-100' }}
                                    {{ $shiftColors[$code]['text'] ?? 'text-gray-800' }}
                                    flex items-center justify-center text-xs font-medium">
                                    {{ $code }}
                                </span>
                                <span class="text-sm">{{ $time }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Schedule Table -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Table Header -->
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-48">
                                Employee Name
                            </th>
                            @foreach($calendar as $day)
                                @if($day['isCurrentMonth'])
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <div>{{ Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                                        <div>{{ Carbon\Carbon::parse($day['date'])->format('d') }}</div>
                                    </th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-1 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $user->username }}
                                </td>
                                @foreach($calendar as $day)
                                    @if($day['isCurrentMonth'])
                                        <td class="px-2 py-1 whitespace-nowrap text-sm text-center">
                                            @php
                                                $schedule = $day['schedules']->first(function($schedule) use ($user) {
                                                    return $schedule->user_id === $user->id;
                                                });
                                            @endphp

                                            @if($schedule)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $shiftColors[$schedule->shift_code]['bg'] ?? 'bg-gray-100 dark:bg-gray-900/20' }}
                                                    {{ $shiftColors[$schedule->shift_code]['text'] ?? 'text-gray-800 dark:text-gray-200' }}">
                                                    {{ $schedule->shift_code }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">-</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
// Get initial month and year from URL or use current date
const currentDate = new Date();
const params = new URLSearchParams(window.location.search);
const currentMonth = params.get('month') ? parseInt(params.get('month')) : currentDate.getMonth() + 1;
const currentYear = params.get('year') ? parseInt(params.get('year')) : currentDate.getFullYear();

// Initialize select elements with current values
document.addEventListener('DOMContentLoaded', function() {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    
    // Set current month and year in selects
    monthSelect.value = currentMonth;
    yearSelect.value = currentYear;
});

function updateFilter() {
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearSelect').value;
    
    const params = new URLSearchParams(window.location.search);
    params.set('month', month);
    params.set('year', year);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}
</script>