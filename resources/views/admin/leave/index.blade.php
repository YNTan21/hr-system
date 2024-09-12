@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Leave</h1>
                
                <form action="{{ route('admin.leave.index') }}" method="GET" class="flex flex-wrap items-end space-x-4 mb-4">
                    <div class="flex-1 min-w-[150px]">
                        <!-- Employee Filter -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Employee Name:</label>
                            <select name="user_id" id="user_id" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Employees</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <!-- Leave Type Filter -->
                        <div class="flex-1">
                            <label for="leave_type_id" class="block">Leave Type:</label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select">
                                <option value="">All Leave Types</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ request('leave_type_id') == $leaveType->id ? 'selected' : '' }}>{{ $leaveType->leave_type }}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <!-- Status Filter -->
                        <div class="flex-1">
                            <label for="status" class="block">Status:</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                
                        <!-- Date Range Filter -->
                        <div class="flex-1">
                            <label for="from_date" class="block">From Date:</label>
                            <input type="date" name="from_date" id="from_date" class="form-input" value="{{ request('from_date') }}">
                        </div>
                        <div>
                            <label for="to_date" class="block">To Date:</label>
                            <input type="date" name="to_date" id="to_date" class="form-input" value="{{ request('to_date') }}">
                        </div>
                    </div>
                
                    <div class="flex space-x-4 mt-4">
                        <!-- Submit Button -->
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Search</button>
                
                        <!-- Reset Button -->
                        <a href="{{ route('admin.leave.index') }}" class="px-4 py-2 bg-gray-300 text-black rounded">Reset</a>
                    </div>
                </form>

                <div class="text-right mb-2">
                    <a href="{{ route('admin.leave.create')}}">
                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Add Leave</button>
                    </a>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">From Date</th>
                            <th scope="col">To Date</th>
                            <th scope="col">Number of Days</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaves as $leave)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                {{-- <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $leaveType->leave_type }}
                                </th> --}}
                                <td class="px-6 py-3">{{ $leave->user->username }}</td>
                                <td class="px-6 py-3">{{ $leave->leaveType->leave_type }}</td>
                                <td class="px-6 py-3">{{ $leave->from_date }}</td>
                                <td class="px-6 py-3">{{ $leave->to_date }}</td>
                                <td class="px-6 py-3">{{ $leave->number_of_days }}</td>
                                <td class="px-6 py-3">{{ $leave->reason }}</td>
                                <td class="px-6 py-3">
                                    @if($leave->status == 'pending')
                                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-lg">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @elseif($leave->status == 'approved')
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-lg">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @elseif($leave->status == 'rejected')
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-lg">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <ul class="list-inline m-0">
                                        <li class="list-inline-item">
                                            <a href="{{ route('admin.leave.edit', $leave->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $leaves->links() }}
                </div>
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