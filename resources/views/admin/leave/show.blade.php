@section('site-title', 'Dashboard')
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

                <div class="col px-5 pb-2">
                    <h3 class="font-bold text-xl text-center">
                        VIEW LEAVE
                    </h3>
                </div>

                <div class="form-group py-3">
                    <label for="user_id" class="font-bold pb-1">EMPLOYEE NAME</label>
                    <input type="text" id="user_id" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $leave->user->username }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="leave_type_id" class="font-bold pb-1">LEAVE TYPE</label>
                    <input type="text" id="leave_type_id" class="form-control rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $leave->leaveType->leave_type }}" readonly>
                </div>

                <div class="flex space-x-4 pb-3">
                    <div class="flex-1">
                        <label for="from_date" class="font-bold pb-1">FROM</label>
                        <input type="date" id="from_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $leave->from_date }}" readonly>
                    </div>
                
                    <div class="flex-1">
                        <label for="to_date" class="font-bold pb-1">TO</label>
                        <input type="date" id="to_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $leave->to_date }}" readonly>
                    </div>
                </div>
                
                <div class="form-group pb-3">
                    <label for="number_of_days" class="font-bold pb-1">NUMBER OF DAYS</label>
                    <input type="number" id="number_of_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" value="{{ $leave->number_of_days }}" readonly>
                </div>

                <div class="form-group pb-3">
                    <label for="reason" class="font-bold pb-1">Reason</label>
                    <textarea id="reason" class="form-control block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" readonly>{{ $leave->reason }}</textarea>
                </div>

                @if($leave->status == 'pending')
                    <div class="flex justify-center space-x-4 mt-2 mb-2">
                        <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn w-60 h-12 bg-green-300 text-green-700 font-bold rounded-lg border-2 border-green-300 hover:bg-white hover:border-green-500 hover:text-green-500 transition-all duration-300">
                                <i class="fa-solid fa-check mr-2"></i> APPROVE
                            </button>
                        </form>
                        <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn w-60 h-12 bg-red-300 text-red-700 font-bold rounded-lg border-2 border-red-300 hover:bg-white hover:border-red-500 hover:text-red-500 transition-all duration-300">
                                <i class="fa-solid fa-times mr-2"></i> REJECT
                            </button>
                        </form>
                    </div>
                @endif

                <div class="col text-center p-2 px-5 pb-3">
                    <a href="{{ route('admin.leave.index') }}" class="btn btn-dark font-bold">BACK TO LIST</a>
                </div>

                </div>
            </div>
        </div>
    </div>
</x-layout.master>
