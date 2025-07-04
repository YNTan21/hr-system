@section('site-title', 'Create Position')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14 max-w-xl mx-auto">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 text-center" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 text-center" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.employee.positions.store') }}" method="post">
                    @csrf
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Position</h2>
                    </div>
                    <!-- Position Name -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-6 flex items-center gap-4 dark:bg-gray-800">
                        <div class="flex items-center min-w-max">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-briefcase text-white text-sm"></i>
                            </div>
                            <label for="positionName" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Position Name</label>
                        </div>
                        <input type="text" 
                               id="positionName" 
                               name="positionName" 
                               value="{{ old('positionName') }}" 
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                    </div>
                    <!-- Status -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-6 flex items-center gap-4 dark:bg-gray-800">
                        <div class="flex items-center min-w-max">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-toggle-on text-white text-sm"></i>
                            </div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Status</label>
                        </div>
                        <div class="flex-1 flex gap-6 items-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="status" value="active" class="form-radio h-4 w-4 text-blue-600" {{ old('status', 'active') == 'active' ? 'checked' : '' }} required>
                                <span class="ml-2 text-sm text-gray-700 dark:text-white">Active</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="status" value="inactive" class="form-radio h-4 w-4 text-blue-600" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-white">Inactive</span>
                            </label>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Add Position
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
