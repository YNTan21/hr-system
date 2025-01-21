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
