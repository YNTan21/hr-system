@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Add and Export Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Attendance List</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.attendance.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus"></i> Add Attendance
                        </a>
                        <a href="{{ route('admin.attendance.export') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filter Form -->
                <form action="{{ route('admin.attendance.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4">
                        <!-- Employee Filter -->
                        <div class="flex-1">
                            <!-- <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label> -->
                            <select name="user_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Employees</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="flex-1">
                            <!-- <label class="block text-sm font-medium text-gray-700 mb-1">Date</label> -->
                            <input type="date" 
                                   name="date" 
                                   value="{{ request('date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Filter
                            </button>
                            
                            <a href="{{ route('admin.attendance.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Attendance Table -->
                <table id="attendanceTable" class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3 px-6">Date</th>
                            <th scope="col" class="py-3 px-6">Employee ID</th>
                            <th scope="col" class="py-3 px-6">Name</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                            <th scope="col" class="py-3 px-6">Clock In</th>
                            <th scope="col" class="py-3 px-6">Clock Out</th>
                            <th scope="col" class="py-3 px-6">Overtime</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances as $attendance)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
                                <td class="py-4 px-6">{{ date('Y-m-d', strtotime($attendance->date)) }}</td>
                                <td class="py-4 px-6">{{ $attendance->user_id }}</td>
                                <td class="py-4 px-6">{{ $attendance->user->username }}</td>
                                <td class="py-4 px-6">
                                    @if($attendance->status == 'on_time')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            On Time
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Late
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">{{ $attendance->clock_in_time }}</td>
                                <td class="py-4 px-6">{{ $attendance->clock_out_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500 bg-white">
                                    No records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.master>