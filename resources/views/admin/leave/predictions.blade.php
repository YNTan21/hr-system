@section('site-title', 'Leave Predictions')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Leave Predictions Overview</h2>
                    {{-- <button onclick="runPrediction()" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Update Predictions
                    </button> --}}
                </div>

                <!-- Leave Trend Graph -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-semibold mb-2">Leave Trend Analysis</h3>
                    @if(file_exists(public_path('images/leave_trend.png')))
                        <img src="{{ asset('images/leave_trend.png') }}" 
                             alt="Leave Trend Graph" 
                             class="w-full h-auto"
                             style="max-height: 300px; object-fit: contain;">
                    @else
                        <div class="text-center text-gray-500 py-4">
                            Graph will be generated after updating predictions
                        </div>
                    @endif
                </div>

                <!-- Compact Predictions Grid -->
                <div class="grid grid-cols-7 gap-1 mb-4">
                    @forelse($predictions->chunk(7) as $week)
                        @foreach($week as $prediction)
                            <div class="bg-white p-2 rounded border hover:shadow-sm transition-shadow">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-xs font-medium text-gray-600">
                                            {{ $prediction->date->format('D') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $prediction->date->format('d/m') }}
                                        </div>
                                    </div>
                                    <div class="text-base font-bold {{ $prediction->predicted_leaves > 5 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $prediction->predicted_leaves }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="col-span-7 text-center py-2 text-sm">
                            No predictions available
                        </div>
                    @endforelse
                </div>

                <!-- Compact Stats -->
                <div class="grid grid-cols-4 gap-2 text-sm">
                    <div class="bg-blue-50 p-2 rounded">
                        <div class="text-xs text-blue-600">Avg/Day</div>
                        <div class="text-base font-bold">
                            {{ $predictions->avg('predicted_leaves') ? number_format($predictions->avg('predicted_leaves'), 1) : '0' }}
                        </div>
                    </div>
                    <div class="bg-red-50 p-2 rounded">
                        <div class="text-xs text-red-600">Peak</div>
                        <div class="text-base font-bold">
                            {{ $predictions->max('predicted_leaves') ?? '0' }}
                        </div>
                    </div>
                    <div class="bg-green-50 p-2 rounded">
                        <div class="text-xs text-green-600">Total</div>
                        <div class="text-base font-bold">
                            {{ $predictions->sum('predicted_leaves') }}
                        </div>
                    </div>
                    <div class="bg-purple-50 p-2 rounded">
                        <div class="text-xs text-purple-600">Days</div>
                        <div class="text-base font-bold">
                            {{ $predictions->count() }}
                        </div>
                    </div>
                </div>

                @if(config('app.debug'))
                    <div class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="text-xs text-white uppercase bg-gray-800">
                                    <tr>
                                        <th class="py-1 px-2">Date</th>
                                        <th class="py-1 px-2">Day</th>
                                        <th class="py-1 px-2">Predicted</th>
                                        <th class="py-1 px-2">Week</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($predictions as $prediction)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-1 px-2">{{ $prediction->date->format('Y-m-d') }}</td>
                                            <td class="py-1 px-2">{{ $prediction->date->format('l') }}</td>
                                            <td class="py-1 px-2">
                                                <span class="px-1.5 py-0.5 rounded text-xs {{ $prediction->predicted_leaves > 5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $prediction->predicted_leaves }}
                                                </span>
                                            </td>
                                            <td class="py-1 px-2">Week {{ $prediction->date->weekOfYear }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function runPrediction() {
        fetch('/admin/leave/predict', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating predictions: ' + data.error);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
    </script>
</x-layout.master> 