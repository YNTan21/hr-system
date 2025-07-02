@section('site-title', 'Create Leave Type')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <!-- Simple Header -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Leave Type</h2>
                </div>
                <form action="{{route('admin.leaveType.store')}}" method="post">
                    @csrf
                    @if (session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif
                    <!-- First Row: Leave Type (Name) -->
                    <div class="mb-6">
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-tag text-white text-sm"></i>
                                </div>
                                <label for="leaveType" class="text-sm font-semibold text-gray-700 dark:text-white">Leave Type</label>
                    </div>
                            <input type="text" id="leaveType" name="leaveType" value="{{ old('leaveType') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @error('leaveType')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>
                    <!-- Second Row: Code and Color -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Code Card -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-barcode text-white text-sm"></i>
                                </div>
                                <label for="leaveCode" class="text-sm font-semibold text-gray-700 dark:text-white">Code</label>
                            </div>
                            <input type="text" id="leaveCode" name="leaveCode" value="{{ old('leaveCode') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @error('leaveCode')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                        <!-- Color Card -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-palette text-white text-sm"></i>
                                </div>
                                <label for="color" class="text-sm font-semibold text-gray-700 dark:text-white">Color</label>
                            </div>
                        <div class="flex items-center gap-3">
                                <input type="color" name="color" id="color" value="#3b82f6"
                                       class="w-8 h-8 rounded-full cursor-pointer p-0 border-0 overflow-hidden appearance-none">
                            <span id="colorCode" class="text-sm text-gray-900 dark:text-white">#3b82f6</span>
                        </div>
                        @error('color')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                    <!-- Third Row: Status -->
                    <div class="mb-6">
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-toggle-on text-white text-sm"></i>
                                </div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-white">Status</label>
                            </div>
                            <div class="flex items-center gap-4 mt-2">
                            <div class="flex items-center">
                                    <input type="radio" id="active" name="status" value="active" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('status') == 'active' ? 'checked' : '' }} required>
                                <label for="active" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                            </div>
                            <div class="flex items-center">
                                    <input type="radio" id="inactive" name="status" value="inactive" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="inactive" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="flex justify-center gap-4 mt-8">
                        <a href="{{ route('admin.leaveType.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                        <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>Add Leave Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorCode = document.getElementById('colorCode');
    colorInput.addEventListener('input', function(e) {
        colorCode.textContent = e.target.value;
    });
});
</script>


