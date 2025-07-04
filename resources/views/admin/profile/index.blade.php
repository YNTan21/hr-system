@section('site-title', 'Profile')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14 w-full relative">
                <a href="{{ route('admin.profile.edit') }}" class="absolute top-4 right-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md text-xs flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <div class="text-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Profile</h2>
                </div>
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- Profile Picture -->
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-blue-200 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="object-cover w-full h-full">
                        @else
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        @endif
                    </div>
                </div>
                <!-- Info Cards Grid: 3 per row for key fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 w-full">
                    <!-- Username -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Username:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->username }}</span>
                    </div>
                    <!-- Email -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-envelope text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Email:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->email }}</span>
                    </div>
                    <!-- Phone -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-phone text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Phone:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <!-- IC -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-id-card text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">NRIC/Passport:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->ic ?? '-' }}</span>
                    </div>
                    <!-- DOB -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-birthday-cake text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">DOB:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->dob ?? '-' }}</span>
                    </div>
                    <!-- Gender -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-venus-mars text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Gender:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ ucfirst($user->gender) ?? '-' }}</span>
                    </div>
                    <!-- Marital Status -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-ring text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Marital:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ ucfirst($user->marital_status) ?? '-' }}</span>
                    </div>
                    <!-- Nationality -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-flag text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Nationality:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ ucfirst($user->nationality) ?? '-' }}</span>
                    </div>
                    <!-- Status -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-toggle-on text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Status:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ ucfirst($user->status) ?? '-' }}</span>
                    </div>
                    <!-- Hire Date -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-calendar-plus text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Hire Date:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->hire_date ?? '-' }}</span>
                    </div>
                    <!-- Position -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Position:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->position ? $user->position->position_name : '-' }}</span>
                    </div>
                    <!-- Employment Type -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-clipboard-list text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Type:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ ucfirst($user->type) ?? '-' }}</span>
                    </div>
                </div>
                <!-- Address (full width) -->
                <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 mb-3 w-full">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                        <i class="fas fa-map-marker-alt text-white text-xs"></i>
                    </div>
                    <span class="font-semibold text-gray-700 dark:text-white">Address:</span>
                    <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->address ?? '-' }}</span>
                </div>
                <!-- Bank Info: 3 per row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full mb-3">
                    <!-- Bank Name -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-building text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Bank Name:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->bank_name ?? '-' }}</span>
                    </div>
                    <!-- Bank Account Holder Name -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-user-circle text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Account Holder:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->bank_account_holder_name ?? '-' }}</span>
                    </div>
                    <!-- Bank Account Number -->
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm flex items-center gap-2 dark:bg-gray-800 w-full">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-1">
                            <i class="fas fa-hashtag text-white text-xs"></i>
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white">Account Number:</span>
                        <span class="flex-1 text-gray-900 dark:text-white text-xs">{{ $user->bank_account_number ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.master>