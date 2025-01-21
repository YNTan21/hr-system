@section('site-title', 'Leave Process')
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
                    <h2 class="text-xl font-semibold">Leave Process List</h2>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.leave.process') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- Employee Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Employees</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Leave Type Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Leave Type</label>
                            <select name="leave_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Leave Types</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ request('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->leave_type }}
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
                            <a href="{{ route('admin.leave.process') }}" 
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
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                            <tr>
                                <th scope="col" class="py-3 px-6">Employee Name</th>
                                <th scope="col" class="py-3 px-6">Leave Type</th>
                                <th scope="col" class="py-3 px-6">From Date</th>
                                <th scope="col" class="py-3 px-6">To Date</th>
                                <th scope="col" class="py-3 px-6">Days</th>
                                <th scope="col" class="py-3 px-6">Reason</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($leaves as $leave)
                                <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                    <td class="py-4 px-6">{{ $leave->user->username }}</td>
                                    <td class="py-4 px-6">{{ $leave->leaveType->leave_type }}</td>
                                    <td class="py-4 px-6">{{ date('Y-m-d', strtotime($leave->from_date)) }}</td>
                                    <td class="py-4 px-6">{{ date('Y-m-d', strtotime($leave->to_date)) }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $leave->number_of_days }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if(strlen($leave->reason) > 30)
                                            {{ substr($leave->reason, 0, 30) }}...
                                        @else
                                            {{ $leave->reason }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($leave->status == 'pending')
                                                bg-yellow-100 text-yellow-800
                                            @elseif($leave->status == 'approved')
                                                bg-green-100 text-green-800
                                            @else
                                                bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex justify-center space-x-2">
                                            @if($leave->status == 'pending')
                                                <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
    document.getElementById('from_date').addEventListener('change', validateDates);
    document.getElementById('to_date').addEventListener('change', validateDates);

    function validateDates() {
        var fromDate = new Date(document.getElementById('from_date').value);
        var toDate = new Date(document.getElementById('to_date').value);

        if (fromDate && toDate && toDate < fromDate) {
            alert('To Date must be after From Date.');
            document.getElementById('to_date').value = '';
        }
    }
</script>