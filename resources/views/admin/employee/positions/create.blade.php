@section('site-title', 'Create Position')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Page Title -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Create Position</h2>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Create Position Form -->
                <form action="{{ route('admin.employee.positions.store') }}" method="post">
                    @csrf
                    
                    <!-- Position Name -->
                    <div class="mb-6">
                        <label for="positionName" class="block text-sm font-medium text-gray-700 mb-2">
                            Position Name
                        </label>
                        <input type="text" 
                               id="positionName" 
                               name="positionName" 
                               value="{{ old('positionName') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                               required>
                        @error('positionName')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <div class="flex space-x-4">
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="active" 
                                       name="status" 
                                       value="active" 
                                       {{ old('status') == 'active' ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       required>
                                <label for="active" class="ml-2 text-sm text-gray-700">
                                    Active
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="inactive" 
                                       name="status" 
                                       value="inactive"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="inactive" class="ml-2 text-sm text-gray-700">
                                    Inactive
                                </label>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
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
