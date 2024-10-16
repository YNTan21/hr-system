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

                <form action="{{ route('admin.employee.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="col px-5 pb-2">
                        <h3 class="title text-center">
                            Create Employee
                        </h3>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Personal Information</h4>
                        
                        <!-- Profile Picture -->
                        <div class="form-group mb-4">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="mt-1 block w-full" accept="image/*" onchange="previewImage(this);">
                            <img id="preview" src="#" alt="Profile Picture Preview" style="display:none; max-width: 200px; max-height: 200px; margin-top: 10px;">
                        </div>

                        <!-- Username and IC -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="username" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="username" id="username" value="{{ old('username') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="flex-1">
                                <label for="ic" class="block text-sm font-medium text-gray-700">NRIC/Passport</label>
                                <input type="text" name="ic" id="ic" value="{{ old('ic') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                        </div>

                        <!-- Date of Birth and Gender -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="dob" id="dob" value="{{ old('dob') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="flex-1">
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender" id="gender" value="{{ old('gender') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Marital Status and Nationality -->
                        <div class="flex space-x-4 mb-4">
                            <div class="flex-1">
                                <label for="marital_status" class="block text-sm font-medium text-gray-700">Marital Status</label>
                                <select name="marital_status" id="marital_status" value="{{ old('marital_status') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                                <select name="nationality" id="nationality" value="{{ old('nationality') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required onchange="toggleNonMalaysian()">
                                    <option value="malaysian">Malaysian</option>
                                    <option value="non-malaysian">Non-Malaysian</option>
                                </select>
                            </div>
                            <div id="non-malaysian-input" class="flex-1" style="display: none;">
                                <label for="specific_nationality" class="block text-sm font-medium text-gray-700">Specify Nationality</label>
                                <input type="text" name="specific_nationality" id="specific_nationality" value="{{ old('specific_nationality') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" value="{{ old('address') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Bank Information</h4>

                        <!-- Bank Name -->
                        <div class="form-group mb-4">
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <select name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select a bank</option>
                                <option value="Affin Bank Berhad">Affin Bank Berhad</option>
                                <option value="Affin Islamic Bank Berhad">Affin Islamic Bank Berhad</option>
                                <option value="Alliance Bank Malaysia Berhad">Alliance Bank Malaysia Berhad</option>
                                <option value="Alliance Islamic Bank Malaysia Berhad">Alliance Islamic Bank Malaysia Berhad</option>
                                <option value="Al Rajhi Banking & Investment Corporation (Malaysia) Berhad">Al Rajhi Banking & Investment Corporation (Malaysia) Berhad</option>
                                <option value="AmBank (M) Berhad">AmBank (M) Berhad</option>
                                <option value="Bank Islam Malaysia Berhad">Bank Islam Malaysia Berhad</option>
                                <option value="Bank Kerjasama Rakyat Malaysia Berhad">Bank Kerjasama Rakyat Malaysia Berhad</option>
                                <option value="Bank Muamalat Malaysia Berhad">Bank Muamalat Malaysia Berhad</option>
                                <option value="Bank of China (Malaysia) Berhad">Bank of China (Malaysia) Berhad</option>
                                <option value="Bank Pertanian Malaysia Berhad (Agrobank)">Bank Pertanian Malaysia Berhad (Agrobank)</option>
                                <option value="Bank SimpananNasional">Bank SimpananNasional</option>
                                <option value="CIMB Bank Berhad">CIMB Bank Berhad</option>
                                <option value="CIMB Islamic Bank Berhad">CIMB Islamic Bank Berhad</option>
                                <option value="Citibank Berhad">Citibank Berhad</option>
                                <option value="Hong Leong Bank Berhad">Hong Leong Bank Berhad</option>
                                <option value="Hong Leong Islamic Bank Berhad">Hong Leong Islamic Bank Berhad</option>
                                <option value="HSBC Amanah Malaysia Berhad">HSBC Amanah Malaysia Berhad</option>
                                <option value="HSBC Bank Malaysia Berhad">HSBC Bank Malaysia Berhad</option>
                                <option value="Industrial and Commercial Bank of China (Malaysia) Berhad">Industrial and Commercial Bank of China (Malaysia) Berhad</option>
                                <option value="Kuwait Finance House">Kuwait Finance House</option>
                                <option value="Malayan Banking Berhad">Malayan Banking Berhad</option>
                                <option value="MBSB Bank Berhad">MBSB Bank Berhad</option>
                                <option value="OCBC Bank (Malaysia) Berhad">OCBC Bank (Malaysia) Berhad</option>
                                <option value="Public Bank Berhad">Public Bank Berhad</option>
                                <option value="RHB Bank Berhad">RHB Bank Berhad</option>
                                <option value="RHB Islamic Bank Berhad">RHB Islamic Bank Berhad</option>
                                <option value="Standard Chartered Bank Malaysia Berhad">Standard Chartered Bank Malaysia Berhad</option>
                                <option value="Standard Chartered Saadiq Berhad">Standard Chartered Saadiq Berhad</option>
                                <option value="United Overseas Bank (Malaysia) Berhad">United Overseas Bank (Malaysia) Berhad</option>
                            </select>
                        </div>

                        <!-- Bank Account Holder Name -->
                        <div class="form-group mb-4">
                            <label for="bank_account_holder_name" class="block text-sm font-medium text-gray-700">Bank Account Holder Name</label>
                            <input type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ old('bank_account_holder_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Bank Account Number -->
                        <div class="form-group mb-4">
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Bank Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Employment Information</h4>

                        <!-- Hire Date -->
                        <div class="form-group mb-4">
                            <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Position -->
                        <div class="form-group mb-4">
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Type -->
                        <div class="form-group mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Employment Type</label>
                            <select name="type" id="type" value="{{ old('type') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-group mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" value="{{ old('status') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email and Password -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="col text-center p-2 px-5">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Create Employee</button>
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
