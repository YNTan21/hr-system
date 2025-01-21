@section('site-title', 'Used Annual Leave')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Employee Info -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Used Annual Leave History</h2>
                        <div class="flex gap-4 text-sm">
                            <div class="flex items-center">
                                <span class="font-medium mr-2">Employee:</span>
                                <span>{{ $usedLeaves->first()->user->username ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-medium mr-2">Leave Balance:</span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $leaveBalance->annual_leave_balance ?? '0' }} days
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-2">
                        <a href="{{ route('admin.annual-leave-balance.export-used-leave', request()->route('userId')) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white text-sm py-2 px-4 rounded">
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

                <!-- Used Leave Table -->
                <table class="w-full text-base text-center text-black-500 dark:text-gray-400">
                    <thead class="text-sm text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-2 px-3">From Date</th>
                            <th scope="col" class="py-2 px-3">To Date</th>
                            <th scope="col" class="py-2 px-3">Number of Days</th>
                            <th scope="col" class="py-2 px-3">Reason</th>
                            <th scope="col" class="py-2 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($usedLeaves as $leave)
                            <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-2 px-3">{{ date('Y-m-d', strtotime($leave->from_date)) }}</td>
                                <td class="py-2 px-3">{{ date('Y-m-d', strtotime($leave->to_date)) }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $leave->number_of_days }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    @if(strlen($leave->reason) > 30)
                                        {{ substr($leave->reason, 0, 30) }}...
                                    @else
                                        {{ $leave->reason }}
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                                        {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Back Button -->
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('admin.annual-leave-balance.index') }}" 
                       class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>