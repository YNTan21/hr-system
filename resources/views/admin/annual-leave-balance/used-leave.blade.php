@section('site-title', 'Used Annual Leave')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>Used Annual Leave</h2>

                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6">Leave Type</th>
                                <th scope="col" class="py-3 px-6">From Date</th>
                                <th scope="col" class="py-3 px-6">To Date</th>
                                <th scope="col" class="py-3 px-6">Number of Days</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usedLeaves as $leave)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-4 px-6">{{ $leave->leaveType->name }}</td>
                                    <td class="py-4 px-6">{{ $leave->from_date }}</td>
                                    <td class="py-4 px-6">{{ $leave->to_date }}</td>
                                    <td class="py-4 px-6">{{ $leave->number_of_days }}</td>
                                    <td class="py-4 px-6">{{ ucfirst($leave->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>