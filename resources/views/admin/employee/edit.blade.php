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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="col px-5 pb-2">
                        <h3 class="title text-center">
                            Edit Employee
                        </h3>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Personal Information</h4>
                        
                        <!-- Profile Picture -->
                        <div class="form-group mb-4">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="mt-1 block w-full" accept="image/*" onchange="previewImage(this);">
                            <img id="preview" src="{{ $employee->profile_picture_url }}" alt="Profile Picture Preview" style="max-width: 200px; max-height: 200px; margin-top: 10px;">
                        </div>

                        <!-- Username and IC -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="username" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="username" id="username" value="{{ old('username', $employee->username) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="flex-1">
                                <label for="ic" class="block text-sm font-medium text-gray-700">NRIC/Passport</label>
                                <input type="text" name="ic" id="ic" value="{{ old('ic', $employee->ic) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                        </div>

                        <!-- Date of Birth and Gender -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="dob" id="dob" value="{{ old('dob', $employee->dob) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="flex-1">
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Marital Status and Nationality -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="marital_status" class="block text-sm font-medium text-gray-700">Marital Status</label>
                                <select name="marital_status" id="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                                <select name="nationality" id="nationality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required onchange="toggleNonMalaysian()">
                                    <option value="malaysian" {{ old('nationality', $employee->nationality) == 'malaysian' ? 'selected' : '' }}>Malaysian</option>
                                    <option value="non-malaysian" {{ old('nationality', $employee->nationality) == 'non-malaysian' ? 'selected' : '' }}>Non-Malaysian</option>
                                </select>
                            </div>
                            <div id="non-malaysian-input" class="flex-1" style="display: {{ old('nationality', $employee->nationality) == 'non-malaysian' ? 'block' : 'none' }};">
                                <label for="specific_nationality" class="block text-sm font-medium text-gray-700">Specify Nationality</label>
                                <input type="text" name="specific_nationality" id="specific_nationality" value="{{ old('specific_nationality', $employee->specific_nationality) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Bank Information</h4>

                        <!-- Bank Name -->
                        <div class="form-group mb-4">
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <select name="bank_name" id="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select a bank</option>
                                <!-- Add all bank options here, with the selected attribute for the current bank -->
                                <option value="Affin Bank Berhad" {{ old('bank_name', $employee->bank_name) == 'Affin Bank Berhad' ? 'selected' : '' }}>Affin Bank Berhad</option>
                                <!-- ... other bank options ... -->
                            </select>
                        </div>

                        <!-- Bank Account Holder Name -->
                        <div class="form-group mb-4">
                            <label for="bank_account_holder_name" class="block text-sm font-medium text-gray-700">Bank Account Holder Name</label>
                            <input type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ old('bank_account_holder_name', $employee->bank_account_holder_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Bank Account Number -->
                        <div class="form-group mb-4">
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Bank Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Employment Information</h4>

                        <!-- Hire Date -->
                        <div class="form-group mb-4">
                            <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Position -->
                        <div class="form-group mb-4">
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position', $employee->position) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Type -->
                        <div class="form-group mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Employment Type</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="full-time" {{ old('type', $employee->type) == 'full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="part-time" {{ old('type', $employee->type) == 'part-time' ? 'selected' : '' }}>Part-time</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-group mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email and Password -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Account Information</h4>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="flex items-center">
                                <span class="mr-4">••••••••</span>
                                <a href="{{ route('admin.employee.edit-password', $employee->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                                    Change Password
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col text-center p-2 px-5">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
function previewImage(input) {
    var preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}

// Initialize Choices.js for bank selection
document.addEventListener('DOMContentLoaded', function() {
    new Choices('#bank_name', {
        searchEnabled: true,
        searchFields: ['label', 'value'],
        searchPlaceholderValue: 'Search for a bank...',
        itemSelectText: '',
        // Disable fuzzy search and make the search more accurate
        fuseOptions: {
            threshold: 0.0, // Setting this to 0 ensures exact matching
        },
    });
});

function toggleNonMalaysian() {
    var nationalitySelect = document.getElementById('nationality');
    var nonMalaysianInput = document.getElementById('non-malaysian-input');
    var specificNationalityInput = document.getElementById('specific_nationality');
    
    if (nationalitySelect.value === 'non-malaysian') {
        nonMalaysianInput.style.display = 'block';
        specificNationalityInput.required = true;
    } else {
        nonMalaysianInput.style.display = 'none';
        specificNationalityInput.required = false;
        specificNationalityInput.value = ''; // Clear the input when hidden
    }
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
