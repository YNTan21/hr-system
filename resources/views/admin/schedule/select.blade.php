@section('site-title', 'Select Schedule')

<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Select Schedule</h1>

                <form action="{{ route('admin.schedule.view') }}" method="GET">
                    @csrf

                    <!-- Year Selection -->
                    <div class="mb-4">
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @for ($i = date('Y'); $i >= 2000; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Month Selection -->
                    <div class="mb-4">
                        <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Week Selection -->
                    <div class="mb-4">
                        <label for="week" class="block text-sm font-medium text-gray-700">Week</label>
                        <select name="week" id="week" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @for ($i = 1; $i <= 5; $i++) <!-- Assuming max 5 weeks in a month -->
                                <option value="{{ $i }}">Week {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex justify-center gap-4 mt-4">
                        <a href="{{ route('admin.schedule.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Timesheet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>