@section('site-title', 'Fingerprint Management')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                
                <!-- Title and Action Buttons -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Fingerprint Management</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('verify.page') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-fingerprint"></i> Verify Attendance
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Employee Table -->
                <table class="w-full text-sm text-center text-black-500 dark:text-gray-400">
                    <thead class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="py-3 px-6">Username</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                            <th scope="col" class="py-3 px-6">Last Updated</th>
                            <th scope="col" class="py-3 px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($employees as $employee)
                            <tr class="bg-white hover:bg-gray-100">
                                <td class="py-4 px-6">{{ $employee->username }}</td>
                                <td class="py-4 px-6">
                                    @if($employee->hasFingerprint)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Registered
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Not Registered
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    {{ $employee->fingerprint_updated_at ? $employee->fingerprint_updated_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex justify-center space-x-2">
                                        @if($employee->hasFingerprint)
                                            <a href="{{ route('remove.fingerprint', ['id' => $employee->id]) }}" 
                                               class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('enroll.page', ['id' => $employee->id]) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    No employees found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($employees->hasPages())
                    <div class="mt-4">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout.master>
