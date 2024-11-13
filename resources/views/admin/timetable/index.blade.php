@section('site-title', 'Employee Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->
            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                    <!-- Header -->
                    <div class="page-header">
                        <div class="flex justify-between items-center">
                            <h3 class="page-title">Timetable</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('form/shiftlist/page') }}">
                                    <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                        Shift
                                    </button>
                                </a>
                                <button type="button" 
                                    onclick="openModal()"
                                    class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                    Assign Shifts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="assignShiftModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Assign Shift</h3>
                <form action="{{ route('shifts.assign') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Employee</label>
                        <select name="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                        <input type="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                        <input type="time" name="start_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">End Time</label>
                        <input type="time" name="end_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeModal()" class="text-gray-500 background-transparent font-bold px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1">
                            Cancel
                        </button>
                        <button type="submit" class="text-white bg-gray-800 hover:bg-gray-900 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this JavaScript at the bottom of your file -->
    <script>
        function openModal() {
            document.getElementById('assignShiftModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('assignShiftModal').classList.add('hidden');
        }
    </script>
</x-layout.master>