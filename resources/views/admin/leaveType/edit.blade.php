@section('site-title', 'Edit Leave Type')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800 dark:text-white">Edit Leave Type</h2>
                </div>

                {{-- Success Message --}}
                @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Leave Type Edit Form -->
                <form action="{{ route('admin.leaveType.update', $leaveType->id) }}" method="post" class="max-w-3xl mx-auto">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- Leave Type -->
                        <div>
                            <label for="leaveType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Leave Type:</label>
                            <input type="text" id="leaveType" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                name="leaveType" 
                                value="{{ old('leaveType', $leaveType->leave_type) }}" 
                                placeholder="Enter leave type"
                                required>
                            @error('leaveType')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Leave Code -->
                        <div>
                            <label for="leaveCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code:</label>
                            <input type="text" id="leaveCode" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                name="leaveCode" 
                                value="{{ old('leaveCode', $leaveType->code) }}" 
                                placeholder="Enter leave code"
                                required>
                            @error('leaveCode')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status:</label>
                            <div class="flex gap-4">
                                <div class="flex items-center">
                                    <input type="radio" id="active" name="status" value="active" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" 
                                        {{ $leaveType->status == 'active' ? 'checked' : '' }}>
                                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300" for="active">Active</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="inactive" name="status" value="inactive" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" 
                                        {{ $leaveType->status == 'inactive' ? 'checked' : '' }}>
                                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300" for="inactive">Inactive</label>
                                </div>
                            </div>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('admin.leaveType.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>Update Leave Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>