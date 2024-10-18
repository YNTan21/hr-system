@section('site-title', 'Employee Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <form action="{{ route('admin.employee.index') }}" method="GET" class="mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Search by name" value="{{ request('search') }}" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300">
                        </div>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
                            Search
                        </button>
                        @if(request()->has('search'))
                            <a href="{{ route('admin.employee.index') }}" class="px-4 py-2 text-white bg-gray-500 rounded-md hover:bg-gray-600 focus:outline-none focus:bg-gray-600">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="text-right mb-2">
                    <a href="{{ route('admin.employee.sCreate')}}">
                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Simple Add Employee</button>
                    </a>
                    <a href="{{ route('admin.employee.create')}}">
                        <button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Add Employee</button>
                    </a>
                </div>
                
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6 w-1/7">Employee Name</th>
                                {{-- <th scope="col" class="py-3 px-6 w-1/7">Department</th> --}}
                                <th scope="col" class="py-3 px-6 w-1/7">Position</th>
                                <th scope="col" class="py-3 px-6 w-1/7">Type</th>
                                <th scope="col" class="py-3 px-6 w-1/7">Hire Date</th>
                                <th scope="col" class="py-3 px-6 w-1/7">Status</th>
                                <th scope="col" class="py-3 px-6 w-1/7">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-4 px-6 w-1/7">{{ $employee->username }}</td>
                                    {{-- <td class="py-4 px-6 w-1/7">{{ $employee->department->name }}</td> --}}
                                    <td class="py-4 px-6 w-1/7">{{ $employee->position }}</td>
                                    <td class="py-4 px-6 w-1/7">{{ ucfirst($employee->type) }}</td>
                                    <td class="py-4 px-6 w-1/7">{{ date('Y-m-d', strtotime($employee->hire_date)) }}</td>
                                    <td class="py-4 px-6 w-1/7">
                                        @if($employee->status == 'active')
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight text-green-700 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-100">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight text-red-700 bg-red-100 rounded-lg dark:bg-red-700 dark:text-red-100">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 px-6 w-1/7">
                                        <a href="{{ route('admin.employee.show', $employee->id) }}" class="btn btn-sm bg-blue-100 text-blue-500">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employee.edit', $employee->id) }}" class="btn btn-sm bg-yellow-100 text-yellow-500">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.employee.destroy', $employee->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-red-100 text-red-500" onclick="return confirm('Are you sure you want to delete this employee?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Display pagination links -->
                    <div>
                        {{ $employees->links() }}
                    </div>

                </div>
                <div class="d-flex justify-content-center">
                    {{ $employees->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>
