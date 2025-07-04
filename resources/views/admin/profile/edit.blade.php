@section('site-title', 'Edit Profile')
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
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Profile</h2>
                    </div>
                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4 flex items-center"><span class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3"><i class="fas fa-user text-white text-sm"></i></span>Personal Information</h4>
                        <!-- Profile Picture -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-image text-white text-sm"></i>
                                </div>
                                <label for="profile_picture" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Profile Picture</label>
                            </div>
                            <input type="file" name="profile_picture" id="profile_picture" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" accept="image/*" onchange="previewImage(this);">
                            <img id="preview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}" alt="Profile Picture Preview" style="max-width: 120px; max-height: 120px; margin-left: 1rem;">
                        </div>
                        <!-- Name and IC -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-id-card text-white text-sm"></i>
                                    </div>
                                    <label for="username" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Full Name</label>
                                </div>
                                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                            </div>
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-passport text-white text-sm"></i>
                                    </div>
                                    <label for="ic" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">NRIC/Passport</label>
                                </div>
                                <input type="text" name="ic" id="ic" value="{{ old('ic', $user->ic) }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                            </div>
                        </div>
                        <!-- Date of Birth and Gender -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-birthday-cake text-white text-sm"></i>
                                    </div>
                                    <label for="dob" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Date of Birth</label>
                                </div>
                                <div class="relative flex-1">
                                    <input type="text" name="dob" id="dob" value="{{ old('dob', $user->dob) }}" class="flex-1 pl-10 rounded-md border-2 border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm py-2" placeholder="Select date" autocomplete="off" required>
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-calendar-alt text-blue-400"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-venus-mars text-white text-sm"></i>
                                    </div>
                                    <label for="gender" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Gender</label>
                                </div>
                                <div class="flex-1 relative overflow-visible">
                                    <button id="dropdownGenderButton" type="button" class="flex-1 bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span id="selectedGender">{{ old('gender', $user->gender) ? ucfirst(old('gender', $user->gender)) : 'Select Gender' }}</span>
                                        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                    </button>
                                    <div id="dropdownGender" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownGenderButton">
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="male">Male</a></li>
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="female">Female</a></li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="gender" id="gender" value="{{ old('gender', $user->gender) }}" required>
                                </div>
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-phone text-white text-sm"></i>
                                </div>
                                <label for="phone" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Phone</label>
                            </div>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                        </div>
                        <!-- Marital Status and Nationality in the same row -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Marital Status -->
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                                <div class="flex items-center min-w-max mb-1">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-ring text-white text-sm"></i>
                                    </div>
                                    <label for="marital_status" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Marital Status</label>
                                </div>
                                <div class="flex-1 relative overflow-visible">
                                    <button id="dropdownMaritalButton" type="button" class="w-full bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span id="selectedMarital">{{ old('marital_status', $user->marital_status) ? ucfirst(old('marital_status', $user->marital_status)) : 'Select Status' }}</span>
                                        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                    </button>
                                    <div id="dropdownMarital" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownMaritalButton">
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="single">Single</a></li>
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="married">Married</a></li>
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="divorced">Divorced</a></li>
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="widowed">Widowed</a></li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="marital_status" id="marital_status" value="{{ old('marital_status', $user->marital_status) }}" required>
                                </div>
                            </div>
                            <!-- Nationality -->
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex flex-col gap-2 dark:bg-gray-800">
                                <div class="flex items-center min-w-max mb-1">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-flag text-white text-sm"></i>
                                    </div>
                                    <label for="nationality" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Nationality</label>
                                </div>
                                <div class="flex-1 relative overflow-visible">
                                    <button id="dropdownNationalityButton" type="button" class="w-full bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span id="selectedNationality">{{ old('nationality', $user->nationality) ? ucfirst(old('nationality', $user->nationality)) : 'Select Nationality' }}</span>
                                        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                    </button>
                                    <div id="dropdownNationality" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownNationalityButton">
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="malaysian">Malaysian</a></li>
                                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="non-malaysian">Non-Malaysian</a></li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="nationality" id="nationality" value="{{ old('nationality', $user->nationality) }}" required>
                                </div>
                                <!-- Specify Nationality (conditionally shown) -->
                                <div id="non-malaysian-input" class="mt-2 flex flex-col gap-1" style="display: {{ old('nationality', $user->nationality) == 'non-malaysian' ? 'flex' : 'none' }};">
                                    <label for="specific_nationality" class="text-sm font-semibold text-gray-700 dark:text-white mb-1">Specify Nationality</label>
                                    <input type="text" name="specific_nationality" id="specific_nationality" value="{{ old('specific_nationality', $user->specific_nationality) }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm w-full">
                                </div>
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-map-marker-alt text-white text-sm"></i>
                                </div>
                                <label for="address" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Address</label>
                            </div>
                            <textarea name="address" id="address" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                    <!-- Bank Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4 flex items-center"><span class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3"><i class="fas fa-university text-white text-sm"></i></span>Bank Information</h4>
                        <!-- Bank Name -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-white text-sm"></i>
                                </div>
                                <label for="bank_name" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Bank Name</label>
                            </div>
                            <div class="flex-1 relative overflow-visible">
                                <button id="dropdownBankButton" type="button" class="flex-1 bg-white border-2 border-blue-200 rounded-lg shadow-sm text-gray-900 text-sm px-5 py-2.5 text-left inline-flex items-center justify-between dark:bg-gray-700 dark:border-blue-400 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span id="selectedBank">{{ old('bank_name', $user->bank_name) ? old('bank_name', $user->bank_name) : 'Select a bank' }}</span>
                                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                </button>
                                <div id="dropdownBank" class="z-50 hidden bg-white border border-blue-200 rounded-lg shadow-lg w-56 left-0 absolute mt-1 dark:bg-white max-h-48 overflow-y-auto">
                                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownBankButton">
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Affin Bank Berhad">Affin Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Affin Islamic Bank Berhad">Affin Islamic Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Alliance Bank Malaysia Berhad">Alliance Bank Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Alliance Islamic Bank Malaysia Berhad">Alliance Islamic Bank Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Al Rajhi Banking & Investment Corporation (Malaysia) Berhad">Al Rajhi Banking & Investment Corporation (Malaysia) Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="AmBank (M) Berhad">AmBank (M) Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank Islam Malaysia Berhad">Bank Islam Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank Kerjasama Rakyat Malaysia Berhad">Bank Kerjasama Rakyat Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank Muamalat Malaysia Berhad">Bank Muamalat Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank of China (Malaysia) Berhad">Bank of China (Malaysia) Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank Pertanian Malaysia Berhad (Agrobank)">Bank Pertanian Malaysia Berhad (Agrobank)</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Bank Simpanan Nasional">Bank Simpanan Nasional</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="CIMB Bank Berhad">CIMB Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="CIMB Islamic Bank Berhad">CIMB Islamic Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Citibank Berhad">Citibank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Hong Leong Bank Berhad">Hong Leong Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Hong Leong Islamic Bank Berhad">Hong Leong Islamic Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="HSBC Amanah Malaysia Berhad">HSBC Amanah Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="HSBC Bank Malaysia Berhad">HSBC Bank Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Industrial and Commercial Bank of China (Malaysia) Berhad">Industrial and Commercial Bank of China (Malaysia) Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Kuwait Finance House">Kuwait Finance House</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Malayan Banking Berhad">Malayan Banking Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="MBSB Bank Berhad">MBSB Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="OCBC Bank (Malaysia) Berhad">OCBC Bank (Malaysia) Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Public Bank Berhad">Public Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="RHB Bank Berhad">RHB Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="RHB Islamic Bank Berhad">RHB Islamic Bank Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Standard Chartered Bank Malaysia Berhad">Standard Chartered Bank Malaysia Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="Standard Chartered Saadiq Berhad">Standard Chartered Saadiq Berhad</a></li>
                                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100" data-value="United Overseas Bank (Malaysia) Berhad">United Overseas Bank (Malaysia) Berhad</a></li>
                                    </ul>
                                </div>
                                <input type="hidden" name="bank_name" id="bank_name" value="{{ old('bank_name', $user->bank_name) }}" required>
                            </div>
                        </div>
                        <!-- Bank Account Holder Name and Number -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-circle text-white text-sm"></i>
                                    </div>
                                    <label for="bank_account_holder_name" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Account Holder Name</label>
                                </div>
                                <input type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ old('bank_account_holder_name', $user->bank_account_holder_name) }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                            </div>
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-hashtag text-white text-sm"></i>
                                    </div>
                                    <label for="bank_account_number" class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Account Number</label>
                                </div>
                                <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required>
                            </div>
                        </div>
                    </div>
                    <!-- Employment Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4 flex items-center"><span class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3"><i class="fas fa-briefcase text-white text-sm"></i></span>Employment Information</h4>
                        <!-- Hire Date and Position -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-plus text-white text-sm"></i>
                                    </div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Hire Date</label>
                                </div>
                                <input type="text" value="{{ $user->hire_date }}" class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" readonly disabled>
                            </div>
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm flex items-center gap-4 dark:bg-gray-800">
                                <div class="flex items-center min-w-max">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-tie text-white text-sm"></i>
                                    </div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Position</label>
                                </div>
                                <input type="text" value="{{ $user->position ? $user->position->position_name : '-' }}" class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" readonly disabled>
                            </div>
                        </div>
                        <!-- Employment Type -->
                        <div class="bg-white border-2 border-blue-200 rounded-lg p-4 shadow-sm mb-4 flex items-center gap-4 dark:bg-gray-800">
                            <div class="flex items-center min-w-max">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-clipboard-list text-white text-sm"></i>
                                </div>
                                <label class="text-sm font-semibold text-gray-700 dark:text-white whitespace-nowrap">Employment Type</label>
                            </div>
                            <input type="text" value="{{ ucfirst($user->type) }}" class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" readonly disabled>
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-6">
                        <a href="{{ route('admin.profile.index') }}" class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg text-xs">
                            <i class="fas fa-save mr-2"></i>Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.master>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#dob", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: true
    });
    flatpickr("#hire_date", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
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
                if(inputId === 'nationality') {
                    document.getElementById('non-malaysian-input').style.display = (value === 'non-malaysian') ? 'flex' : 'none';
                }
            });
        });
        document.addEventListener('click', function(event) {
            var dropdown = document.getElementById(dropdownId);
            var button = document.getElementById(buttonId);
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
    setupDropdown('dropdownGenderButton', 'dropdownGender', 'gender', 'selectedGender');
    setupDropdown('dropdownMaritalButton', 'dropdownMarital', 'marital_status', 'selectedMarital');
    setupDropdown('dropdownNationalityButton', 'dropdownNationality', 'nationality', 'selectedNationality');
    setupDropdown('dropdownBankButton', 'dropdownBank', 'bank_name', 'selectedBank');
    setupDropdown('dropdownPositionButton', 'dropdownPosition', 'position_id', 'selectedPosition');
    setupDropdown('dropdownTypeButton', 'dropdownType', 'type', 'selectedType');
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('nationality').value === 'non-malaysian') {
            document.getElementById('non-malaysian-input').style.display = 'flex';
        }
    });
    function previewImage(input) {
        var preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script> 