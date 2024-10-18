@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
            </div>
            <!-- Main Content -->

            <div class="p-4 sm:ml-64">
                <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white pb-4 text-center">Annual Leave Balance</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Annual Leave Balance (Days)</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user) 
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6 w-1/6">{{ $user->username }}</td>
                                <td class="px-6 py-3">{{ $user->annual_leave_balance }}</td>
                                <td class="px-6 py-3">
                                    <ul class="list-inline m-0">
                                        <li class="list-inline-item">
                                            <a href="" class="btn btn-sm btn-primary" style="font-size: 12px">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                {{-- {{ route('admin.leaveBalance.edit', $user->id) }} --}}
                                            </a>
                                        </li>
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


