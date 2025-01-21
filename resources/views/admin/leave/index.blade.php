@section('site-title', 'Leave Management')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Leave Management</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.annual-leave-balance.index') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Annual Leave
                        </a>
                        <a href="{{ route('admin.leave.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus"></i> Add Leave
                        </a>
                        <a href="{{ route('admin.leave.export') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.leave.index') }}" method="GET" class="mb-6">
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

                        <!-- Year Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = 2010; // Extended back to 2010
                                    $endYear = $currentYear + 10; // Extended forward 10 years
                                @endphp
                                <option value="">Select Year</option>
                                @for($year = $endYear; $year >= $startYear; $year--)
                                    <option value="{{ $year }}" {{ (request('year', $currentYear) == $year) ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
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

                        <!-- Status Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Filter Date -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Filter Date</label>
                            <input type="date" name="filter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" 
                                   value="{{ request('filter_date') }}">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex space-x-2">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.leave.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

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
                                            <a href="{{ route('admin.leave.show', $leave->id) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.leave.edit', $leave->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.leave.destroy', $leave->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this leave request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @if($leave->status == 'pending')
                                                <form action="{{ route('admin.leave.approve', $leave->id) }}" 
                                                      method="POST" 
                                                      class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900"
                                                            title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.leave.reject', $leave->id) }}" 
                                                      method="POST" 
                                                      class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Reject">
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