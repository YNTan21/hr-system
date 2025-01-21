@section('site-title', 'Edit Annual Leave Balance')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                {{-- Success Message --}}
                @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                <div class="col px-5 pb-4">
                    <h3 class="text-2xl font-bold text-center text-gray-900 dark:text-white">
                        Edit Annual Leave Balance
                    </h3>
                </div>

                <form action="{{ route('admin.annual-leave-balance.update', $leaveBalance->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6 px-5">
                        <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employee Name</label>
                        <select name="user_id" id="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $leaveBalance->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 px-5">
                        <label for="annual_leave_balance" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Annual Leave Days</label>
                        <input type="number" 
                               name="annual_leave_balance" 
                               id="annual_leave_balance" 
                               value="{{ $leaveBalance->annual_leave_balance }}"
                               required 
                               min="0"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                               placeholder="Enter annual leave days">
                        @error('annual_leave_balance')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-center gap-4 px-5">
                        <a href="{{ route('admin.annual-leave-balance.index') }}" 
                           class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" 
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Update Leave Balance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>