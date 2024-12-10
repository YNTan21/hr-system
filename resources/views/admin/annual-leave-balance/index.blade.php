@section('site-title', 'Annual Leave Balance')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>Annual Leave Balances</h2>

                <!-- Filter Form -->
                <form action="{{ route('admin.leave.index') }}" method="GET" class="mb-4">
                    <div class="flex flex-wrap items-end justify-between">
                        <div class="flex flex-row space-x-4 flex-grow">
                            <!-- Name Filter -->
                            <div class="flex-1">
                                <input type="text" name="name" id="name" placeholder="Search by Name" class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ request('name') }}">
                            </div>

                            <!-- Year Filter -->
                            <div class="flex-1">
                                <select name="year" id="year" class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Year</option>
                                    @for ($i = 2020; $i <= date('Y'); $i++)
                                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 ml-4">
                            <!-- Submit Button -->
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Filter</button>
                            <!-- Reset Button -->
                            <a href="{{ route('admin.leave.index') }}" class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400 transition-colors">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6">Employee Name</th>
                                <th scope="col" class="py-3 px-6">Leave Balance</th>
                                <th scope="col" class="py-3 px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $leaveBalance = $leaveBalances->firstWhere('user_id', $user->id);
                                @endphp
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-4 px-6">{{ $user->username }}</td>
                                    <td class="py-4 px-6">
                                        {{ $leaveBalance ? $leaveBalance->annual_leave_balance : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if ($leaveBalance)
                                            <a href="{{ route('admin.annual-leave-balance.edit', $leaveBalance->id) }}" class="btn btn-sm bg-yellow-100 text-yellow-500">Edit</a>
                                            <a href="{{ route('admin.annual-leave-balance.showUsedLeave', $user->id) }}" class="btn btn-sm bg-green-100 text-green-500">View Used Leave</a>
                                        @else
                                            <a href="{{ route('admin.annual-leave-balance.create', ['user_id' => $user->id]) }}" class="btn btn-sm bg-blue-100 text-blue-500">Add</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $leaveBalances->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.master>