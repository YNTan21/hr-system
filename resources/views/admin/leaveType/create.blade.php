@section('site-title', 'Dashboard')
<x-layout.master>
    <div class="container-fluid">
        <div class="row border-b shadow-lg fixed top-0 right-0 left-0 bg-white dark:bg-gray-800 z-10">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md dark:bg-gray-800 mt-20">
                <form action="{{route('admin.leaveType.store')}}" method="post" class="p-6">
                    @csrf
                    {{-- Success Message --}}
                    @if (session('success'))
                    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-center text-gray-900 dark:text-white">
                            Create Leave Type
                        </h3>
                    </div>

                    <!-- Leave Type Input -->
                    <div class="mb-6 px-5">
                        <label for="leaveType" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Leave Type</label>
                        <input type="text" id="leaveType" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 shadow-md" 
                               name="leaveType" 
                               value="{{ old('leaveType') }}" 
                               required>
                        @error('leaveType')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Leave Code -->
                    <div class="mb-6 px-5">
                        <label for="leaveCode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Code</label>
                        <input type="text" id="leaveCode" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 shadow-md" 
                               name="leaveCode" 
                               value="{{ old('leaveCode') }}" 
                               required>
                        @error('leaveCode')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Leave Color -->
                    <div class="mb-6 px-5">
                        <label for="color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="color" 
                                       name="color" 
                                       id="color" 
                                       value="#3b82f6"
                                       class="w-8 h-8 rounded-full cursor-pointer p-0 border-0 overflow-hidden appearance-none">
                                <style>
                                    input[type="color"]::-webkit-color-swatch-wrapper {
                                        padding: 0;
                                    }
                                    input[type="color"]::-webkit-color-swatch {
                                        border: none;
                                        border-radius: 50%;
                                    }
                                    input[type="color"]::-moz-color-swatch {
                                        border: none;
                                        border-radius: 50%;
                                    }
                                </style>
                            </div>
                            <span id="colorCode" class="text-sm text-gray-900 dark:text-white">#3b82f6</span>
                        </div>
                        @error('color')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Row -->
                    <div class="mb-8 px-5">
                        <div class="flex items-center justify-center gap-4">
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="active" 
                                       name="status" 
                                       value="active" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                                       {{ old('status') == 'active' ? 'checked' : '' }} 
                                       required>
                                <label for="active" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="inactive" 
                                       name="status" 
                                       value="inactive" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="inactive" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-4 px-5">
                        <a href="{{ route('admin.leaveType.index') }}" 
                           class="text-gray-700 hover:text-gray-900 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-all duration-200 dark:text-gray-300 dark:hover:text-white">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" 
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 shadow-md transition-all duration-200">
                            Add Leave Type
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

    // Update color code when color changes
    colorInput.addEventListener('input', function(e) {
        colorCode.textContent = e.target.value;
    });
});
</script>


