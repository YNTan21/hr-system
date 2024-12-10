@section('site-title', 'Add Annual Leave Balance')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>

        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h2>Add Annual Leave Balance for {{ $user->username }}</h2>

                <form action="{{ route('admin.annual-leave-balance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    
                    <div class="mb-4">
                        <label for="annual_leave_balance" class="block text-sm font-medium text-gray-700">Annual Leave Days</label>
                        <input type="number" name="annual_leave_balance" id="annual_leave_balance" required class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter annual leave days">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Add Leave Balance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>