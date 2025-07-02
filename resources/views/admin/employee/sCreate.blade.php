@section('site-title', 'Create Employee')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
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
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <form action="{{ route('admin.employee.store') }}" method="post">
                    @csrf
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Employee</h2>
                    </div>
                    <!-- Name -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                        <div class="flex items-center min-w-max">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-id-card text-white text-sm"></i>
                            </div>
                            <label for="username" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Full Name</label>
                        </div>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                    </div>
                    <!-- Email and Password in the same row -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Email -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                            <div class="flex items-center min-w-max mb-1">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-white text-sm"></i>
                                </div>
                                <label for="email" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Email</label>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                    </div>
                    <!-- Password -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                            <div class="flex items-center min-w-max mb-1">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-lock text-white text-sm"></i>
                                </div>
                                <label for="password" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Password</label>
                            </div>
                            <input type="password" name="password" id="password" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                        </div>
                    </div>
                    <!-- Hire Date, Position, Employment Type with label above input -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <!-- Hire Date -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                            <label for="hire_date" class="text-sm font-semibold text-gray-700 dark:text-white mb-1">Hire Date</label>
                            <div class="relative flex-1">
                                <input type="text" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" class="pl-10 rounded-md border-2 border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" placeholder="Select date" autocomplete="off" required>
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-blue-400"></i>
                                </span>
                            </div>
                        </div>
                        <!-- Position -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                            <label for="position_id" class="text-sm font-semibold text-gray-700 dark:text-white mb-1">Position</label>
                            <div class="flex-1 relative overflow-visible">
                                <button id="dropdownPositionButton" type="button" class="w-full bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span id="selectedPosition">{{ old('position_id') ? ($positions->firstWhere('id', old('position_id'))?->position_name ?? 'Select Position') : 'Select Position' }}</span>
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                </button>
                                <div id="dropdownPosition" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white max-h-48 overflow-y-auto">
                                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownPositionButton">
                                @foreach($positions as $position)
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="{{ $position->id }}">{{ $position->position_name }}</a></li>
                                @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" name="position_id" id="position_id" value="{{ old('position_id') }}" required>
                            </div>
                            @error('position_id')
                                <p class="ml-4 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Type -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                            <label for="type" class="text-sm font-semibold text-gray-700 dark:text-white mb-1">Employment Type</label>
                            <div class="flex-1 relative overflow-visible">
                                <button id="dropdownTypeButton" type="button" class="w-full bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span id="selectedType">{{ old('type') ? ucfirst(old('type')) : 'Select Type' }}</span>
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                </button>
                                <div id="dropdownType" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownTypeButton">
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="full-time">Full-time</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="part-time">Part-time</a></li>
                                    </ul>
                                </div>
                                <input type="hidden" name="type" id="type" value="{{ old('type') }}" required>
                            </div>
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                        <div class="flex items-center min-w-max">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-toggle-on text-white text-sm"></i>
                            </div>
                            <label for="status" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Status</label>
                        </div>
                        <div class="flex-1 relative overflow-visible">
                            <button id="dropdownStatusButton" type="button" class="flex-1 bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span id="selectedStatus">{{ old('status') ? ucfirst(old('status')) : 'Select Status' }}</span>
                                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                            </button>
                            <div id="dropdownStatus" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownStatusButton">
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="active">Active</a></li>
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="inactive">Inactive</a></li>
                                </ul>
                            </div>
                            <input type="hidden" name="status" id="status" value="{{ old('status') }}" required>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('admin.employee.index') }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-save mr-2"></i>Create Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Flatpickr for hire date
    flatpickr("#hire_date", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
    // Custom dropdown logic for all dropdowns
    function setupDropdown(buttonId, dropdownId, inputId, selectedId) {
        document.getElementById(buttonId).addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById(dropdownId).classList.toggle('hidden');
        });
        document.querySelectorAll(`#${dropdownId} a[data-value]`).forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var value = this.getAttribute('data-value');
                var text = this.textContent;
                document.getElementById(inputId).value = value;
                document.getElementById(selectedId).textContent = text;
                document.getElementById(dropdownId).classList.add('hidden');
            });
        });
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(event) {
            var dropdown = document.getElementById(dropdownId);
            var button = document.getElementById(buttonId);
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
    setupDropdown('dropdownPositionButton', 'dropdownPosition', 'position_id', 'selectedPosition');
    setupDropdown('dropdownTypeButton', 'dropdownType', 'type', 'selectedType');
    setupDropdown('dropdownStatusButton', 'dropdownStatus', 'status', 'selectedStatus');
</script>
