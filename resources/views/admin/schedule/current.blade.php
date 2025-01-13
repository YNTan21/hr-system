@section('site-title', 'Current Week Schedule')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Current Week Schedule</h1>

                <!-- Week Display -->
                <div class="flex justify-center mb-4">
                    <span class="text-lg font-medium">{{ $currentWeekStart->format('d M Y') }} - {{ $currentWeekEnd->format('d M Y') }}</span>
                </div>

                <!-- Timesheet Table -->
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
                                                
                                                // 根据shift_code获取对应的时间
                                                $times = [
                                                    'M' => ['start' => 8.75, 'end' => 18.25], // 8:45 - 18:15
                                                    'A' => ['start' => 12, 'end' => 21],      // 12:00 - 21:00
                                                    'M1' => ['start' => 9.5, 'end' => 12.5],  // 9:30 - 12:30
                                                    'F' => ['start' => 8.75, 'end' => 21],    // 8:45 - 21:00
                                                    'A2' => ['start' => 18, 'end' => 21],     // 18:00 - 21:00
                                                ];
                                                
                                                $shiftTimes = $times[$schedule->shift_code] ?? null;
                                                if (!$shiftTimes) return false;
                                                
                                                return $scheduleDate === $currentDate && 
                                                       $hour >= floor($shiftTimes['start']) && 
                                                       $hour < ceil($shiftTimes['end']);
                                            });
                                        @endphp
                                        <td class="border px-4 py-2 text-center">
                                            @if ($matchingSchedules->isNotEmpty())
                                                @foreach ($matchingSchedules as $schedule)
                                                    <div class="mb-1 px-2 py-1 bg-blue-200 text-blue-900 rounded">
                                                        {{ $schedule->user ? $schedule->user->username : 'No user found' }}
                                                        <br>
                                                        <span class="text-sm text-blue-700">{{ $schedule->shift_code }}</span>
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
            </div>
        </div>
    </div>
</x-layout.master>