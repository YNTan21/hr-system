@section('site-title', 'Timesheet')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Timesheet</h1>

                <!-- Week Display -->
                <div class="flex justify-center mb-4">
                    <span class="text-lg font-medium">{{ $currentWeekStart->format('d M Y') }} - {{ $currentWeekEnd->format('d M Y') }}</span>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.schedule.timesheet') }}" method="GET" class="mb-6">
                    <div class="flex gap-4">
                        <!-- Week Filter -->
                        <div class="flex-1">
                            <input type="week" 
                                   name="week" 
                                   value="{{ request('week') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Filter
                            </button>
                            
                            <a href="{{ route('admin.schedule.timesheet') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Timesheet Table -->
                @php
                    // 定义一组预设的颜色类
                    $colors = [
                        'bg-blue-200 text-blue-900',
                        'bg-green-200 text-green-900',
                        'bg-purple-200 text-purple-900',
                        'bg-yellow-200 text-yellow-900',
                        'bg-pink-200 text-pink-900',
                        'bg-indigo-200 text-indigo-900',
                        'bg-red-200 text-red-900',
                        'bg-orange-200 text-orange-900',
                        'bg-teal-200 text-teal-900',
                        'bg-cyan-200 text-cyan-900',

                        // 添加更深色调
                        'bg-blue-300 text-blue-900',
                        'bg-green-300 text-green-900',
                        'bg-purple-300 text-purple-900',
                        'bg-yellow-300 text-yellow-900',
                        'bg-pink-300 text-pink-900',
                        'bg-indigo-300 text-indigo-900',
                        'bg-red-300 text-red-900',
                        'bg-orange-300 text-orange-900',
                        'bg-teal-300 text-teal-900',
                        'bg-cyan-300 text-cyan-900',
                        
                        // 添加更浅色调
                        'bg-blue-100 text-blue-900',
                        'bg-green-100 text-green-900',
                        'bg-purple-100 text-purple-900',
                        'bg-yellow-100 text-yellow-900',
                        'bg-pink-100 text-pink-900',
                        'bg-indigo-100 text-indigo-900',
                        'bg-red-100 text-red-900',
                        'bg-orange-100 text-orange-900',
                        'bg-teal-100 text-teal-900',
                        'bg-cyan-100 text-cyan-900'
                    ];
                    
                    // 创建用户ID到颜色的映射
                    $userColors = [];
                    $colorIndex = 0;
                    foreach($schedules->pluck('user.id')->unique() as $userId) {
                        $userColors[$userId] = $colors[$colorIndex % count($colors)];
                        $colorIndex++;
                    }
                @endphp

                <div class="overflow-x-auto mb-8">
                    <table class="w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-100">Time</th>
                                @for ($i = 0; $i < 7; $i++)
                                    <th class="px-4 py-2 bg-gray-100 text-center">{{ $currentWeekStart->copy()->addDays($i)->format('M d (D)') }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($hour = 9; $hour <= 21; $hour++)
                                <tr>
                                    <td class="border px-4 py-2 text-center font-medium bg-gray-50">{{ sprintf('%02d:00', $hour) }}</td>
                                    @for ($day = 0; $day < 7; $day++)
                                        @php
                                            $currentDate = $currentWeekStart->copy()->addDays($day)->format('Y-m-d');
                                            $matchingSchedules = $schedules->filter(function ($schedule) use ($currentDate, $hour) {
                                                $scheduleDate = \Carbon\Carbon::parse($schedule->shift_date)->format('Y-m-d');
                                                $scheduleStartHour = \Carbon\Carbon::parse($schedule->start_time)->hour;
                                                $scheduleEndHour = \Carbon\Carbon::parse($schedule->end_time)->hour;
                                                return $scheduleDate === $currentDate && $hour >= $scheduleStartHour && $hour < $scheduleEndHour;
                                            });
                                        @endphp
                                        <td class="border px-4 py-2 text-center">
                                            @if ($matchingSchedules->isNotEmpty())
                                                @foreach ($matchingSchedules as $schedule)
                                                    <div class="mb-1 px-2 py-1 rounded {{ $userColors[$schedule->user->id] ?? 'bg-gray-200 text-gray-900' }}">
                                                        {{ $schedule->user ? $schedule->user->username : 'No user found' }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- Color Legend -->
                <!-- <div class="mt-4 p-4 bg-white rounded-lg shadow">
                    <h3 class="font-semibold mb-2">Employee Legend:</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($schedules->pluck('user')->unique() as $user)
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded mr-2 {{ $userColors[$user->id] ?? 'bg-gray-200' }}"></span>
                                <span>{{ $user->username }}</span>
                            </div>
                        @endforeach
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</x-layout.master>
