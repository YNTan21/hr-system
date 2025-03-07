@section('site-title', 'Leave Types')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-3 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Page Title and Buttons -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Leave Types List</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.leaveType.create')}}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white text-base py-1.5 px-3 rounded">
                            <i class="fas fa-plus"></i> Add Leave Type
                        </a>
                        <a href="{{ route('admin.leaveType.export') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white text-base py-1.5 px-3 rounded">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded relative mb-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Leave Types Table -->
                <table class="w-full text-base text-center text-black-500 dark:text-gray-400">
                    <thead class="text-sm text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th class="py-2 px-4 w-12">Color</th>
                            <th class="py-2 px-4">Leave Type</th>
                            <th class="py-2 px-4">Leave Code</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($leaveTypes as $leaveType)
                            <tr class="bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-2 px-4">
                                    <div class="flex justify-center">
                                        <div class="w-6 h-6 rounded-full border border-gray-200" 
                                             style="background-color: {{ $leaveType->color }};"></div>
                                    </div>
                                </td>
                                <td class="py-2 px-4">{{ $leaveType->leave_type }}</td>
                                <td class="py-2 px-4">{{ $leaveType->code }}</td>
                                <td class="py-2 px-4">
                                    @if($leaveType->status == 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('admin.leaveType.edit', $leaveType->id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900"
                                                data-modal-target="deleteConfirmationModal-{{ $leaveType->id }}" 
                                                data-modal-toggle="deleteConfirmationModal-{{ $leaveType->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div id="deleteConfirmationModal-{{ $leaveType->id }}" tabindex="-1" 
                                         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                <div class="p-5 text-center">
                                                    <h3 class="mb-4 text-lg font-normal text-gray-500 dark:text-gray-400">
                                                        Are you sure you want to delete this leave type?
                                                    </h3>
                                                    <form action="{{ route('admin.leaveType.destroy', $leaveType->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-4 py-2 mr-2">
                                                            Yes, delete it
                                                        </button>
                                                        <button type="button" 
                                                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-4 py-2 hover:text-gray-900 focus:z-10"
                                                                data-modal-hide="deleteConfirmationModal-{{ $leaveType->id }}">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.master>


