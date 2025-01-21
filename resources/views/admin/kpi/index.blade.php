@section('site-title', 'KPI Management')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Goal Management</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.kpi.export') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.kpi.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- Position Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">All Positions</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->position_name }}
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
                            <a href="{{ route('admin.kpi.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- KPI Table -->
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                            <tr>
                                <th scope="col" class="py-3 px-6">Position Name</th>
                                <th scope="col" class="py-3 px-6">KPI Status</th>
                                <th scope="col" class="py-3 px-6">Goals Count</th>
                                <th scope="col" class="py-3 px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($positions as $position)
                                <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                    <td class="py-4 px-6">{{ $position->position_name }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ !$position->has_kpi ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ !$position->has_kpi ? 'No KPI' : 'KPI Set' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $position->goals_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('admin.kpi.manage', $position->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-cog"></i> Manage KPI
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>
