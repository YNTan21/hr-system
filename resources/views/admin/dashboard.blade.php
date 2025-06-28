@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Welcome Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome to HR Dashboard</h1>
                    <p class="text-gray-600">{{ now()->format('l, F j, Y') }}</p>
                </div>

                <!-- Main Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Employees Card -->
                    <div class="bg-white border-2 border-blue-500 rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-blue-500 text-4xl">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold mb-1 text-gray-800">{{ $totalEmployees }}</p>
                                <h5 class="text-sm font-medium text-gray-600">Total Employees</h5>
                            </div>
                        </div>
                    </div>

                    <!-- On Time Card -->
                    <div class="bg-white border-2 border-green-500 rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-green-500 text-4xl">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold mb-1 text-gray-800">{{ $onTime }}</p>
                                <h5 class="text-sm font-medium text-gray-600">On Time Today</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Late Card -->
                    <div class="bg-white border-2 border-yellow-500 rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 text-4xl">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold mb-1 text-gray-800">{{ $late }}</p>
                                <h5 class="text-sm font-medium text-gray-600">Late Today</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Card -->
                    <div class="bg-white border-2 border-red-500 rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-red-500 text-4xl">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold mb-1 text-gray-800">{{ $leave }}</p>
                                <h5 class="text-sm font-medium text-gray-600">On Leave Today</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Status Tables -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Present Employees -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-green-600 flex items-center">
                            <i class="fas fa-user-check mr-2"></i>
                            Present Today ({{ $presentCount }})
                        </h3>
                        <div class="max-h-48 overflow-y-auto">
                            @forelse($presentEmployees as $user)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">{{ $user->username }}</span>
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Present</span>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm">No present employees</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Absent Employees -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-red-600 flex items-center">
                            <i class="fas fa-user-times mr-2"></i>
                            Absent Today ({{ $absentCount }})
                        </h3>
                        <div class="max-h-48 overflow-y-auto">
                            @forelse($absentEmployees as $user)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">{{ $user->username }}</span>
                                    <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Absent</span>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm">No absent employees</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- On Leave Employees -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 flex items-center">
                            <i class="fas fa-user-clock mr-2"></i>
                            On Leave Today ({{ $onLeaveCount }})
                        </h3>
                        <div class="max-h-48 overflow-y-auto">
                            @forelse($onLeaveEmployees as $user)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">{{ $user->username }}</span>
                                    <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">On Leave</span>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm">No employees on leave</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Position Statistics -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-building text-purple-500 mr-2"></i>
                        Department/Position Statistics
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($positionStats as $stat)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">{{ $stat['position_name'] }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Employees:</span>
                                        <span class="font-semibold">{{ $stat['employee_count'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Present Today:</span>
                                        <span class="font-semibold text-green-600">{{ $stat['present_today'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Attendance Rate:</span>
                                        <span class="font-semibold text-blue-600">{{ $stat['attendance_rate'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pending Leave Requests -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-yellow-400 mb-8">
                    <h3 class="text-lg font-semibold text-yellow-700 mb-4 flex items-center">
                        <i class="fas fa-hourglass-half mr-2"></i> Pending Leave Requests ({{ $pendingLeaves->count() }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="bg-yellow-100">
                                    <th class="py-3 px-4 font-medium">Employee</th>
                                    <th class="py-3 px-4 font-medium">From</th>
                                    <th class="py-3 px-4 font-medium">To</th>
                                    <th class="py-3 px-4 font-medium">Reason</th>
                                    <th class="py-3 px-4 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingLeaves as $leave)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $leave->user->username ?? 'N/A' }}</td>
                                        <td class="py-3 px-4">{{ $leave->from_date->format('Y-m-d') }}</td>
                                        <td class="py-3 px-4">{{ $leave->to_date->format('Y-m-d') }}</td>
                                        <td class="py-3 px-4">{{ $leave->reason }}</td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.leave.edit', $leave->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Review</a>
                                            <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm" onclick="return confirm('Approve this leave request?')">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PUT')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Reject this leave request?')">Reject</button>
                                            </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-400 py-6">No pending leave requests</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overtime Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                            Employee Overtime Hours
                        </h3>
                        <select id="monthSelector" class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="chartLoading" class="text-center py-8 hidden">
                        <div class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading chart data...
                        </div>
                    </div>
                    <div id="chartError" class="text-center py-8 text-red-500 hidden"></div>
                    <canvas id="overtimeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('overtimeChart').getContext('2d');
            var overtimeChart;
            const loadingEl = document.getElementById('chartLoading');
            const errorEl = document.getElementById('chartError');
            const chartEl = document.getElementById('overtimeChart');

            function showLoading() {
                loadingEl.classList.remove('hidden');
                errorEl.classList.add('hidden');
                chartEl.classList.add('hidden');
            }

            function hideLoading() {
                loadingEl.classList.add('hidden');
                chartEl.classList.remove('hidden');
            }

            function showError(message) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                chartEl.classList.add('hidden');
            }

            function updateChart(month) {
                showLoading();
                
                fetch(`/api/overtime-data/${month}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.message || 'Error loading data');
                        }
                        
                        hideLoading();
                        
                        if (overtimeChart) {
                            overtimeChart.destroy();
                        }
                        
                        if (data.usernames.length === 0) {
                            showError('No overtime data available for this month');
                            return;
                        }
                        
                        overtimeChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.usernames,
                                datasets: [{
                                    label: `Overtime Hours - ${document.getElementById('monthSelector').options[document.getElementById('monthSelector').selectedIndex].text}`,
                                    data: data.overtimeHours,
                                    backgroundColor: 'rgba(99, 102, 241, 0.5)',
                                    borderColor: 'rgba(99, 102, 241, 1)',
                                    borderWidth: 2,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Hours'
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Failed to load overtime data: ' + error.message);
                    });
            }

            document.getElementById('monthSelector').addEventListener('change', function() {
                updateChart(this.value);
            });

            // Initial load of current month data
            updateChart(new Date().getMonth() + 1);
        });
    </script>
</x-layout.master>


