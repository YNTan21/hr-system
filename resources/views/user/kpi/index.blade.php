@section('site-title', 'My KPI')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">My KPI - {{ $currentYear }}</h2>
                </div>

                <!-- Year Filter Form -->
                <form action="{{ route('user.kpi.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- Year Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex space-x-2">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('user.kpi.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Monthly KPI Table -->
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="py-3 px-3">Month</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($months as $monthNum => $monthName)
                                @php
                                    $submittedCount = isset($existingEntries[$monthNum]) ? count($existingEntries[$monthNum]) : 0;
                                    $totalGoals = $goals->count();
                                    $statusClass = $submittedCount === $totalGoals ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                @endphp
                                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                    <td class="py-2.5 px-3">{{ $monthName }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $submittedCount }}/{{ $totalGoals }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <a href="{{ route('user.kpi.manage', ['month' => $monthNum, 'year' => $currentYear]) }}" 
                                           class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>