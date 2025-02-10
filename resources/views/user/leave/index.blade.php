@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Title, Leave Balance, and Apply Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-semibold">Leave List</h2>
                        <!-- Annual Leave Balance -->
                        <div class="mt-2 text-sm">
                            <span class="font-medium text-gray-600">Annual Leave Balance:</span>
                            <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                {{ auth()->user()->annual_leave_balance ?? 0 }} days remaining
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <!-- Quick Apply Annual Leave -->
                        <a href="{{ route('user.leave.create', ['type' => 'annual']) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-calendar"></i> Apply Annual Leave
                        </a>
                        <!-- General Apply Leave -->
                        <a href="{{ route('user.leave.create')}}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus"></i> Apply Other Leave
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('user.leave.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- Month Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Month</label>
                            <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Months</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Years</option>
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
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
                            <a href="{{ route('user.leave.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Leave Table -->
                <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3 px-6">Leave Type</th>
                            <th scope="col" class="py-3 px-6">From Date</th>
                            <th scope="col" class="py-3 px-6">To Date</th>
                            <th scope="col" class="py-3 px-6">Number of Days</th>
                            <th scope="col" class="py-3 px-6">Reason</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                            <th scope="col" class="py-3 px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($leaves as $leave)
                            <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6">{{ $leave->leaveType->leave_type }}</td>
                                <td class="py-4 px-6">{{ $leave->from_date }}</td>
                                <td class="py-4 px-6">{{ $leave->to_date }}</td>
                                <td class="py-4 px-6">{{ $leave->number_of_days }}</td>
                                <td class="py-4 px-6">{{ $leave->reason }}</td>
                                <td class="py-4 px-6">
                                    @if($leave->status == 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @elseif($leave->status == 'approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @elseif($leave->status == 'rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($leave->status == 'pending')
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('user.leave.edit', $leave->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('user.leave.destroy', $leave->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this leave request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500 bg-white">
                                    No records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $leaves->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
    document.getElementById('from_date')?.addEventListener('change', validateDates);
    document.getElementById('to_date')?.addEventListener('change', validateDates);

    function validateDates() {
        var fromDate = new Date(document.getElementById('from_date').value);
        var toDate = new Date(document.getElementById('to_date').value);

        if (fromDate && toDate && toDate < fromDate) {
            alert('To Date must be after From Date.');
            document.getElementById('to_date').value = '';
        }
    }
</script>