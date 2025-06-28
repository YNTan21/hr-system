@php
    use App\Models\KpiEntry;
    use App\Models\KPIGoal;
@endphp
@section('site-title', 'KPI Entry')
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
                    <h2 class="text-xl font-semibold">KPI Entry Management - {{ $months[$currentMonth] }} {{ $currentYear }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.kpi.kpiEntry.export', ['user_id' => $selectedUser->id, 'month' => $currentMonth, 'year' => $currentYear]) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form action="{{ route('admin.kpi.kpiEntry.index') }}" method="GET" class="mb-6">
                    <div class="flex gap-4 items-end">
                        <!-- User Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUser->id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Month</label>
                            <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
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
                            <a href="{{ route('admin.kpi.kpiEntry.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- KPI Table with Alpine.js -->
                <div x-data="{
                    show: false,
                    message: '',
                    action: null,
                    modalType: '',
                    title: '',
                    openModal(message, action, title = 'Confirm Action', type = 'confirm') {
                        this.show = true;
                        this.message = message;
                        this.action = action;
                        this.modalType = type;
                        this.title = title;
                    },
                    confirm() {
                        if (this.action) {
                            this.action();
                        }
                        this.closeModal();
                    },
                    closeModal() {
                        this.show = false;
                        this.message = '';
                        this.action = null;
                        this.modalType = '';
                        this.title = '';
                    }
                }">
                    <div class="overflow-x-auto relative">
                        <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                            <thead class="text-sm text-white uppercase bg-gray-800 dark:bg-gray-900">
                                <tr>
                                    <th class="py-3 px-3 w-12">#</th>
                                    <th class="py-3 px-3 w-48">Goal Name</th>
                                    <th class="py-3 px-3 w-16">Goal Score</th>
                                    <th class="py-3 px-3">Category Ranges</th>
                                    <th class="py-3 px-3 w-28">Actual Result</th>
                                    <th class="py-3 px-3 w-28">Actual Score</th>
                                    <th class="py-3 px-3 w-28">Final Score</th>
                                    <th class="py-3 px-3 w-28">Status</th>
                                    <th class="py-3 px-3 w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @php
                                    $totalFinalScore = 0;
                                    $totalGoalScore = 0;
                                @endphp
                                @forelse($goals as $index => $kpigoal)
                                    @php
                                        $totalGoalScore += $kpigoal->goal_score;
                                        if(isset($existingEntries[$kpigoal->id])) {
                                            $totalFinalScore += $existingEntries[$kpigoal->id]->final_score;
                                        }
                                    @endphp
                                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-2.5 px-3">{{ $index + 1 }}</td>
                                        <td class="py-2.5 px-3">
                                            <div class="whitespace-normal break-words text-left font-semibold">
                                                {{ $kpigoal->goal_name }}
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-3">{{ $kpigoal->goal_score }}</td>
                                        <td class="py-2.5 px-3 text-left">
                                            @php
                                                $ranges = json_decode($kpigoal->category_score_ranges, true);
                                                $categoryNames = [
                                                    'category_1' => 'Failed (0)',
                                                    'category_2' => 'Below Expectation (1)',
                                                    'category_3' => 'Threshold (2)',
                                                    'category_4' => 'Meet Target (3)',
                                                    'category_5' => 'Excellence (4)'
                                                ];
                                            @endphp
                                            @foreach($ranges as $category => $range)
                                                <div class="text-xs leading-4">
                                                    <span class="font-semibold">{{ $categoryNames[$category] }}:</span>
                                                    {{ $range['min'] }} - {{ $range['max'] }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="py-2.5 px-3 bg-blue-100 font-semibold text-blue-800">
                                            {{ $existingEntries[$kpigoal->id]->actual_result ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 bg-yellow-100 font-semibold text-yellow-800">
                                            {{ $existingEntries[$kpigoal->id]->actual_score ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 bg-green-100 font-semibold text-green-800">
                                            {{ $existingEntries[$kpigoal->id]->final_score ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if(isset($existingEntries[$kpigoal->id]) && !$existingEntries[$kpigoal->id]->trashed())
                                                @if($existingEntries[$kpigoal->id]->status === 'approved')
                                                    <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($existingEntries[$kpigoal->id]->status === 'pending')
                                                    <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ $existingEntries[$kpigoal->id]->status ?? 'Unknown' }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    No Entry
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="flex justify-center space-x-2">
                                                @if(isset($existingEntries[$kpigoal->id]) && !$existingEntries[$kpigoal->id]->trashed())
                                                    @if($existingEntries[$kpigoal->id]->status === 'approved')
                                                        <!-- Approved 状态: 编辑和还原按钮 -->
                                                        <a href="{{ route('admin.kpi.kpiEntry.edit', $existingEntries[$kpigoal->id]->id) }}"
                                                           class="text-blue-600 hover:text-blue-900"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" 
                                                              action="{{ route('admin.kpi.kpiEntry.revert', $existingEntries[$kpigoal->id]->id) }}" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="button" 
                                                                    class="text-yellow-600 hover:text-yellow-900"
                                                                    title="Revert to Pending"
                                                                    @click="openModal(
                                                                        'Are you sure you want to revert this entry to pending?',
                                                                        () => { $el.closest('form').submit(); },
                                                                        'Confirm Revert',
                                                                        'confirm'
                                                                    )">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($existingEntries[$kpigoal->id]->status === 'pending')
                                                        <!-- Pending 状态: 编辑、批准和还原按钮 -->
                                                        <a href="{{ route('admin.kpi.kpiEntry.edit', $existingEntries[$kpigoal->id]->id) }}"
                                                           class="text-blue-600 hover:text-blue-900"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" 
                                                              action="{{ route('admin.kpi.kpiEntry.approve', $existingEntries[$kpigoal->id]->id) }}" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="button" 
                                                                    class="text-green-600 hover:text-green-900"
                                                                    title="Approve"
                                                                    @click="openModal(
                                                                        'Are you sure you want to approve this entry?',
                                                                        () => { $el.closest('form').submit(); },
                                                                        'Confirm Approve',
                                                                        'confirm'
                                                                    )">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" 
                                                              action="{{ route('admin.kpi.kpiEntry.revert', $existingEntries[$kpigoal->id]->id) }}" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="button" 
                                                                    class="text-yellow-600 hover:text-yellow-900"
                                                                    title="Revert to Previous"
                                                                    @click="openModal(
                                                                        'Are you sure you want to revert this entry?',
                                                                        () => { $el.closest('form').submit(); },
                                                                        'Confirm Revert',
                                                                        'confirm'
                                                                    )">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <!-- Other status: 编辑按钮 -->
                                                        <a href="{{ route('admin.kpi.kpiEntry.edit', $existingEntries[$kpigoal->id]->id) }}"
                                                           class="text-blue-600 hover:text-blue-900"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <span class="text-xs text-gray-500" title="Status: {{ $existingEntries[$kpigoal->id]->status }}">
                                                            {{ $existingEntries[$kpigoal->id]->status }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <!-- No Entry 状态: 创建按钮 -->
                                                    <a href="{{ route('admin.kpi.kpiEntry.create', [
                                                        'goal_id' => $kpigoal->id,
                                                        'user_id' => $selectedUser->id,
                                                        'month' => $currentMonth,
                                                        'year' => $currentYear
                                                    ]) }}"
                                                       class="text-green-600 hover:text-green-900"
                                                       title="Create">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                @endif
                                                
                                                <!-- 历史按钮 -->
                                                @if(isset($hasRevertedHistory[$kpigoal->id]) && $hasRevertedHistory[$kpigoal->id])
                                                    <button type="button" 
                                                            class="text-gray-600 hover:text-gray-900"
                                                            title="View History"
                                                            @click="fetch(`{{ route('admin.kpi.kpiEntry.history', [
                                                                'goal_id' => $kpigoal->id,
                                                                'user_id' => $selectedUser->id,
                                                                'month' => $currentMonth,
                                                                'year' => $currentYear
                                                            ]) }}`)
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                let tableRows = '';
                                                                if (data.entries.length === 0) {
                                                                    tableRows = `<tr>
                                                                        <td colspan='3' class='border border-gray-300 px-4 py-2 text-center'>No history found</td>
                                                                    </tr>`;
                                                                } else {
                                                                    tableRows = data.entries.map(entry => `
                                                                        <tr>
                                                                            <td class='border border-gray-300 px-4 py-2 text-center'>${entry.sequence}</td>
                                                                            <td class='border border-gray-300 px-4 py-2 text-center'>${entry.date}</td>
                                                                            <td class='border border-gray-300 px-4 py-2 text-center'>${entry.result}</td>
                                                                        </tr>
                                                                    `).join('');
                                                                }
                                                                
                                                                openModal(
                                                                    `<table class='min-w-full border-collapse border border-gray-300'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th class='border border-gray-300 px-4 py-2 text-center bg-gray-50'>No</th>
                                                                                <th class='border border-gray-300 px-4 py-2 text-center bg-gray-50'>Date</th>
                                                                                <th class='border border-gray-300 px-4 py-2 text-center bg-gray-50'>Result</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            ${tableRows}
                                                                        </tbody>
                                                                    </table>`,
                                                                    null,
                                                                    'View History',
                                                                    'view'
                                                                )
                                                            })">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-2.5 px-3 text-center">
                                            No goals found for this position
                                        </td>
                                    </tr>
                                @endforelse
                                <!-- Total Row -->
                                <tr class="bg-gray-100 font-semibold">
                                    <td colspan="2" class="py-3 px-3 text-right">Total</td>
                                    <td class="py-3 px-3">{{ $totalGoalScore }}</td>
                                    <td colspan="3" class="py-3 px-3"></td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-1 inline-flex text-base leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $totalFinalScore }}
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3 px-3"></td>
                                </tr>

                                <!-- Total Percentage -->
                                <tr class="bg-gray-100 font-semibold">
                                    <td colspan="9" class="py-3 px-3 text-right">
                                        Total Achievement:
                                        <span class="text-blue-600">
                                            {{ $totalGoalScore > 0 ? round(($totalFinalScore / $totalGoalScore) * 100, 2) : 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <template x-teleport="body">
                        <div x-show="show"
                             class="fixed inset-0 z-50 overflow-y-auto"
                             style="display: none;">
                            <!-- Overlay -->
                            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                            <!-- Modal Content -->
                            <div class="flex min-h-screen items-center justify-center p-4">
                                <div x-show="show"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:max-w-lg">
                                    
                                    <!-- Modal Header -->
                                    <div class="bg-white px-6 pt-5 pb-4">
                                        <div class="text-center sm:text-left">
                                            <template x-if="modalType === 'confirm'">
                                                <div class="flex items-start">
                                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100">
                                                        <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                                                    </div>
                                                    <div class="ml-4 text-left">
                                                        <h3 class="text-lg font-medium leading-6 text-gray-900" x-text="title"></h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500" x-text="message"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="modalType === 'view'">
                                                <div>
                                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" x-text="title"></h3>
                                                    <div x-html="message"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                                        <template x-if="modalType === 'confirm'">
                                            <div class="flex space-x-2">
                                                <button type="button"
                                                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                        @click="confirm()">
                                                    Confirm
                                                </button>
                                                <button type="button"
                                                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                        @click="closeModal()">
                                                    Cancel
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="modalType === 'view'">
                                            <button type="button"
                                                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    @click="closeModal()">
                                                Close
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>
