@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Button to go to Annual Leave Page -->
                <div class="text-right mb-4">
                    <a href="{{ route('admin.annual-leave-balance.index') }}">
                        <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Annual Leave</button>
                    </a>
                </div>

                <form action="{{ route('admin.leave.index') }}" method="GET" class="mb-4">
                    <div class="flex flex-wrap items-end justify-between">
                        <div class="flex flex-row space-x-4 flex-grow">
                            <!-- Employee Filter -->
                            <div class="flex-1">
                                <select name="user_id" id="user_id" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Employees</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <!-- Leave Type Filter -->
                            <div class="flex-1">
                                <select name="leave_type_id" id="leave_type_id" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Leave Types</option>
                                    @foreach ($leaveTypes as $leaveType)
                                        <option value="{{ $leaveType->id }}" {{ request('leave_type_id') == $leaveType->id ? 'selected' : '' }}>{{ $leaveType->leave_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <!-- Status Filter -->
                            <div class="flex-1">
                                <select name="status" id="status" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                    
                            <!-- Date Filter -->
                            <div class="flex-1">
                                <input type="date" name="filter_date" id="filter_date" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ request('filter_date') }}">
                            </div>
                        </div>
                    
                        <div class="flex justify-end space-x-4 ml-4">
                            <!-- Submit Button -->
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Search</button>
                    
                            <!-- Reset Button -->
                            <a href="{{ route('admin.leave.index') }}" class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400 transition-colors">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="text-right mb-2">
                    <a href="{{ route('admin.leave.create') }}">
                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Add Leave</button>
                    </a>
                </div>
                
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6 w-1/6">Employee Name</th>
                                <th scope="col" class="py-3 px-6 w-1/10">Leave Type</th>
                                <th scope="col" class="py-3 px-6 w-1/6">From Date</th>
                                <th scope="col" class="py-3 px-6 w-1/6">To Date</th>
                                <th scope="col" class="py-3 px-6 w-1/12">Days</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Reason</th>
                                <th scope="col" class="py-3 px-6 w-1/12">Status</th>
                                <th scope="col" class="py-3 px-6 w-1/6">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaves as $leave)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-4 px-6 w-1/6">{{ $leave->user->username }}</td>
                                    <td class="py-4 px-6 w-1/10">{{ $leave->leaveType->leave_type }}</td>
                                    <td class="py-4 px-6 w-1/6">{{ $leave->from_date }}</td>
                                    <td class="py-4 px-6 w-1/6">{{ $leave->to_date }}</td>
                                    <td class="py-4 px-6 w-1/12">{{ $leave->number_of_days }}</td>
                                    <td class="py-4 px-6 w-1/6">
                                        @if(strlen($leave->reason) > 50)
                                            {{ substr($leave->reason, 0, 30) }}...
                                        @else
                                            {{ $leave->reason }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 w-1/12">
                                        @if($leave->status == 'pending')
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight text-orange-700 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-100">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        @elseif($leave->status == 'approved')
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight text-green-700 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-100">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        @elseif($leave->status == 'rejected')
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight text-red-700 bg-red-100 rounded-lg dark:bg-red-700 dark:text-red-100">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 px-6 w-1/6">
                                        @if($leave->status == 'pending')
                                            <a href="{{ route('admin.leave.show', $leave->id) }}" class="btn btn-sm bg-blue-100 text-blue-500">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm bg-green-100 text-green-500" style="font-size: 15px; margin-right: 5px;">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm bg-red-100 text-red-500" style="font-size: 15px;">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.leave.show', $leave->id) }}" class="btn btn-sm bg-blue-100 text-blue-500">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
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