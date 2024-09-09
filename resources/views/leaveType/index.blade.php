@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                    <h1>Leave Types</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Leave Code</th>
                            <th scope="col">Status</th>
                            <th scope="col">Deduct Annual Leave</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveTypes as $leaveType)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $leaveType->leave_type }}
                                </th>
                                <td class="px-6 py-3">{{ $leaveType->code }}</td>
                                <td class="px-6 py-3">{{ $leaveType->status }}</td>
                                <td class="px-6 py-3">{{ $leaveType->deduct_annual_leave ? 'Yes' : 'No'}}</td>
                                <td class="px-6 py-3">
                                    <ul class="list-inline m-0">
                                        {{-- <li class="list-inline-item">
                                            <button class="btn btn-success btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                        </li>
                                        <li class="list-inline-item">
                                            <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
                                        </li> --}}
                                        <li class="list-inline-item">
                                            {{-- <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Edit</button> --}}
                                            <a href="{{ route('leaveType.edit', $leaveType->id) }}" class="btn btn-sm btn-primary" style="font-size: 12px">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            {{-- <button type="button" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete</button> --}}
                                            {{-- <form action="{{ route('leaveType.destroy', $leaveType->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the leave type: {{ $leaveType->leave_type }}?');">
                                                    <i class="fa-solid fa-trash" style="font-size: 18px;"></i>
                                                </button>
                                            </form> --}}

                                            <!-- Delete Button -->

                                            <button type="button" class="btn btn-sm btn-danger" style="font-size: 12px;"  data-modal-target="deleteConfirmationModal-{{ $leaveType->id }}" data-modal-toggle="deleteConfirmationModal-{{ $leaveType->id }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                            <!-- Modal HTML -->
                                            <div id="deleteConfirmationModal-{{ $leaveType->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full">
                                                <div class="relative w-full max-w-md max-h-full">
                                                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                        <!-- Modal content -->
                                                        <div class="p-6 text-center">
                                                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete the leave type: <strong>{{ $leaveType->leave_type }}</strong>?</h3>
                                                            <form action="{{ route('leaveType.destroy', $leaveType->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2">
                                                                    Yes, I'm sure
                                                                </button>
                                                                <button type="button" data-modal-hide="deleteConfirmationModal-{{ $leaveType->id }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                                                    No, cancel
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </li>
                                        {{-- <li>
                                            <button type="button" class="btn btn-primary">Edit</button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-danger">Delete</button>
                                        </li> --}}
                                    </ul>
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


