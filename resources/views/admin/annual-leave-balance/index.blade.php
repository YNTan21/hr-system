@section('site-title', 'Annual Leave Balance')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Annual Leave Balances</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.annual-leave-balance.export') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.annual-leave-balance.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="name" 
                                   placeholder="Search by name" 
                                   value="{{ request('name') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </div>
                        <div class="flex-1">
                            <select name="year" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">Select Year</option>
                                @for ($i = 2020; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Search
                            </button>
                            <a href="{{ route('admin.annual-leave-balance.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Reset
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

                <!-- Annual Leave Balance Table -->
                <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3 px-6">Employee Name</th>
                            <th scope="col" class="py-3 px-6">Leave Balance</th>
                            <th scope="col" class="py-3 px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            @php
                                $leaveBalance = $leaveBalances->firstWhere('user_id', $user->id);
                            @endphp
                            <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6">{{ $user->username }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $leaveBalance ? $leaveBalance->annual_leave_balance : 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex justify-center space-x-2">
                                        @if ($leaveBalance)
                                            <a href="{{ route('admin.annual-leave-balance.edit', $leaveBalance->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.annual-leave-balance.showUsedLeave', $user->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.annual-leave-balance.create', ['user_id' => $user->id]) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $leaveBalances->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.master>