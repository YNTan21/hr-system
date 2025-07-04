@section('site-title', 'PIN Settings')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <div class="max-w-xl mx-auto">
                    <h2 class="text-2xl font-bold mb-6">Change System PIN</h2>

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if(isset($currentPin) && Auth::user() && Auth::user()->is_admin)
                        <!-- View PIN & History Button -->
                        <button type="button" onclick="document.getElementById('pinHistoryModal').classList.remove('hidden')"
                            class="mb-6 bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            View Current PIN & History
                        </button>

                        <!-- Modal -->
                        <div id="pinHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full p-6 relative">
                                <button onclick="document.getElementById('pinHistoryModal').classList.add('hidden')"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <h3 class="text-lg font-bold mb-4">Current & History PINs</h3>
                                <div class="mb-4">
                                    <span class="font-semibold">Current PIN:</span>
                                    <span class="ml-2 tracking-widest">{{ $currentPin }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">PIN Change History (last 10):</span>
                                    <div class="overflow-x-auto mt-2">
                                        <table class="min-w-full text-sm border">
                                            <thead>
                                                <tr class="bg-gray-100 dark:bg-gray-700">
                                                    <th class="px-3 py-2 border">Date</th>
                                                    <th class="px-3 py-2 border">Changed By</th>
                                                    <th class="px-3 py-2 border">PIN</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pinHistories as $history)
                                                    <tr>
                                                        <td class="px-3 py-2 border">{{ $history->created_at->format('Y-m-d H:i') }}</td>
                                                        <td class="px-3 py-2 border">
                                                            @if($history->changed_by && $history->changed_by == Auth::id())
                                                                You
                                                            @elseif($history->changed_by && $history->user)
                                                                {{ $history->user->name ?? 'User #'.$history->changed_by }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 border tracking-widest">{{ $history->pin }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="3" class="text-center py-2">No history found.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.pin.update') }}" method="POST" class="space-y-6" id="pinForm">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current PIN</label>
                            <div class="flex justify-center space-x-2 mb-4">
                                @for ($i = 1; $i <= 6; $i++)
                                    <input type="password" 
                                           maxlength="1"
                                           class="w-10 h-10 text-center border rounded text-xl pin-input"
                                           data-group="current"
                                           required>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New PIN</label>
                            <div class="flex justify-center space-x-2 mb-4">
                                @for ($i = 1; $i <= 6; $i++)
                                    <input type="password" 
                                           maxlength="1"
                                           class="w-10 h-10 text-center border rounded text-xl pin-input"
                                           data-group="new"
                                           required>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New PIN</label>
                            <div class="flex justify-center space-x-2 mb-4">
                                @for ($i = 1; $i <= 6; $i++)
                                    <input type="password" 
                                           maxlength="1"
                                           class="w-10 h-10 text-center border rounded text-xl pin-input"
                                           data-group="confirm"
                                           required>
                                @endfor
                            </div>
                        </div>

                        <!-- Hidden inputs for combined values -->
                        <input type="hidden" name="current_pin" id="current_pin">
                        <input type="hidden" name="new_pin" id="new_pin">
                        <input type="hidden" name="confirm_pin" id="confirm_pin">

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update PIN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pinForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Combine PIN values
            ['current', 'new', 'confirm'].forEach(group => {
                const inputs = document.querySelectorAll(`input[data-group="${group}"]`);
                const combinedValue = Array.from(inputs).map(input => input.value).join('');
                document.getElementById(`${group}_pin`).value = combinedValue;
            });
            
            this.submit();
        });

        // Handle input navigation
        document.querySelectorAll('.pin-input').forEach((input, index, inputs) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    const nextInput = inputs[index + 1];
                    if (nextInput) nextInput.focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) {
                    const prevInput = inputs[index - 1];
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.value = '';
                    }
                }
            });
        });
    </script>
</x-layout.master>
