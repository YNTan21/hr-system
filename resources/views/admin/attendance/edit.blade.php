@section('site-title', 'Edit Attendance')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->
            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                {{-- Success Message --}}
                @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <!-- Employee Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $attendance->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" 
                                   name="date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('date', $attendance->date ? date('Y-m-d', strtotime($attendance->date)) : '') }}"
                                   required>
                        </div>

                        <!-- Clock In Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clock In Time</label>
                            <input type="time" 
                                   name="clock_in_time" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('clock_in_time', $attendance->clock_in_time ? date('H:i', strtotime($attendance->clock_in_time)) : '') }}">
                        </div>

                        <!-- Clock Out Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clock Out Time</label>
                            <input type="time" 
                                   name="clock_out_time" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('clock_out_time', $attendance->clock_out_time ? date('H:i', strtotime($attendance->clock_out_time)) : '') }}">
                        </div>

                        <!-- Overtime -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Overtime (hours)</label>
                            <input type="number" 
                                   name="overtime" 
                                   step="0.01" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   value="{{ old('overtime', $attendance->overtime) }}"
                                   min="0"
                                   placeholder="Enter overtime hours">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="on_time" {{ $attendance->status == 'on_time' ? 'selected' : '' }}>On Time</option>
                                <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>Late</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-between mt-4">
                            <a href="{{ route('admin.attendance.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Attendance
                            </button>
                        </div>
                    </div>
                </form>
                
                </div>
            </div>
        </div>
    </div>
</x-layout.master> 