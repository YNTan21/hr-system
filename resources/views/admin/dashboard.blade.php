@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Employees Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-1 border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="text-blue-500 text-3xl">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold text-gray-600 mb-1">{{ $totalEmployees }}</p>
                                <h5 class="text-sm font-medium text-gray-600">Total Employees</h5>
                            </div>
                        </div>
                    </div>

                    <!-- On Time Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-1 border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="text-green-500 text-3xl">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold text-gray-600 mb-1">{{ $onTime }}</p>
                                <h5 class="text-sm font-medium text-gray-600">On Time Today</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Late Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-1 border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 text-3xl">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold text-gray-600 mb-1">{{ $late }}</p>
                                <h5 class="text-sm font-medium text-gray-600">Late Today</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-1 border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="text-red-500 text-3xl">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="flex flex-col items-end">
                                <p class="text-4xl font-bold text-gray-600 mb-1">{{ $leave }}</p>
                                <h5 class="text-sm font-medium text-gray-600">On Leave Today</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Status Tables -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <!-- Present Employees -->
                    <div class="bg-white rounded-lg shadow-md p-4 border border-gray-300">
                        <h3 class="text-lg font-semibold mb-2 text-green-600">Present Today ({{ $presentCount }})</h3>
                        <ul class="text-sm text-gray-700 max-h-48 overflow-y-auto">
                            @forelse($presentEmployees as $user)
                                <li class="py-1 border-b">{{ $user->username }}</li>
                            @empty
                                <li class="text-gray-400">No present employees</li>
                            @endforelse
                        </ul>
                    </div>
                    <!-- Absent Employees -->
                    <div class="bg-white rounded-lg shadow-md p-4 border border-gray-300">
                        <h3 class="text-lg font-semibold mb-2 text-red-600">Absent Today ({{ $absentCount }})</h3>
                        <ul class="text-sm text-gray-700 max-h-48 overflow-y-auto">
                            @forelse($absentEmployees as $user)
                                <li class="py-1 border-b">{{ $user->username }}</li>
                            @empty
                                <li class="text-gray-400">No absent employees</li>
                            @endforelse
                        </ul>
                    </div>
                    <!-- On Leave Employees -->
                    <div class="bg-white rounded-lg shadow-md p-4 border border-gray-300">
                        <h3 class="text-lg font-semibold mb-2 text-blue-600">On Leave Today ({{ $onLeaveCount }})</h3>
                        <ul class="text-sm text-gray-700 max-h-48 overflow-y-auto">
                            @forelse($onLeaveEmployees as $user)
                                <li class="py-1 border-b">{{ $user->username }}</li>
                            @empty
                                <li class="text-gray-400">No employees on leave</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Pending Leave Requests -->
                <div class="mt-8 bg-white rounded-lg shadow-md p-6 border-2 border-yellow-400">
                    <h3 class="text-lg font-semibold text-yellow-700 mb-4 flex items-center">
                        <i class="fas fa-hourglass-half mr-2"></i> Pending Leave Requests ({{ $pendingLeaves->count() }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="bg-yellow-100">
                                    <th class="py-2 px-3">Employee</th>
                                    <th class="py-2 px-3">From</th>
                                    <th class="py-2 px-3">To</th>
                                    <th class="py-2 px-3">Reason</th>
                                    <th class="py-2 px-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingLeaves as $leave)
                                    <tr class="border-b">
                                        <td class="py-2 px-3">{{ $leave->user->username ?? 'N/A' }}</td>
                                        <td class="py-2 px-3">{{ $leave->from_date->format('Y-m-d') }}</td>
                                        <td class="py-2 px-3">{{ $leave->to_date->format('Y-m-d') }}</td>
                                        <td class="py-2 px-3">{{ $leave->reason }}</td>
                                        <td class="py-2 px-3">
                                            <a href="{{ route('admin.leave.edit', $leave->id) }}" class="text-blue-600 hover:underline">Review</a>
                                            <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="ml-2 text-green-600 hover:underline" onclick="return confirm('Approve this leave request?')">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="ml-2 text-red-600 hover:underline" onclick="return confirm('Reject this leave request?')">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-400 py-3">No pending leave requests</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overtime Chart -->
                <div class="mt-8 bg-white rounded-lg shadow-md p-6 border-2 border-gray-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-600">Employee Overtime Hours</h3>
                        <select id="monthSelector" class="border rounded-md px-3 py-1">
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="chartLoading" class="text-center py-4 hidden">Loading...</div>
                    <div id="chartError" class="text-center py-4 text-red-500 hidden"></div>
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
                                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
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

            // 初始加载当月数据
            updateChart(new Date().getMonth() + 1);
        });
    </script>
</x-layout.master>


