@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <div class="text-right mb-2">
                    <a href="{{ route('user.leave.create')}}">
                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Apply for Leave</button>
                    </a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
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