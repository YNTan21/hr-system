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
                                                $scheduleStartHour = \Carbon\Carbon::parse($schedule->start_time)->hour;
                                                $scheduleEndHour = \Carbon\Carbon::parse($schedule->end_time)->hour;
                                                return $scheduleDate === $currentDate && $hour >= $scheduleStartHour && $hour < $scheduleEndHour;
                                            });
                                        @endphp
                                        <td class="border px-4 py-2 text-center">
                                            @if ($matchingSchedules->isNotEmpty())
                                                @foreach ($matchingSchedules as $schedule)
                                                    <div class="mb-1 px-2 py-1 bg-blue-200 text-blue-900 rounded">
                                                        <!-- {{ $schedule->user ? $schedule->user->username : 'No user found' }} -->
                                                        {{ $schedule->shift_code ?? 'No shift code' }}
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