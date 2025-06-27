<x-layout.master>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PIN Verification Overlay -->
    <div id="pin-overlay" class="fixed inset-0 bg-gray-100 flex items-center justify-center z-50">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold mb-2">PIN Verification Required</h3>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-4 text-center">
                    Enter 6-digit PIN
                </label>
                
                <div class="flex justify-center space-x-2 mb-4">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="0">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="1">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="2">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="3">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="4">
                    <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="5">
                </div>

                <div id="result" class="mt-4 p-3 rounded-md text-center hidden"></div>
            </div>
        </div>
    </div>

    <!-- Main Login Content (Initially Hidden) -->
    <div id="login-content" class="hidden main-wrapper min-h-screen bg-gray-100 flex items-center">
        <div class="container mx-auto">
            <!-- Biometric Options -->
            <div class="max-w-md mx-auto mb-6">
                <h3 class="text-2xl font-bold text-center mb-4">Clock In and Clock Out</h3>
                <div class="flex gap-4">
                    <a href="{{ route('attendance.facial-recognition') }}" 
                       class="flex-1 flex items-center justify-center gap-2 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-camera text-xl"></i>
                        <span>Face</span>
                    </a>
                    {{-- <a href="{{ route('verify.page') }}" 
                       class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-fingerprint text-xl"></i>
                        <span>Fingerprint</span>
                    </a> --}}
                </div>
            </div>

            <!-- Login Box -->
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-2">Login</h3>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- General Error Message -->
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" 
                               name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <label for="password" class="block text-gray-700 text-sm font-bold">Password</label>
                        </div>
                        <input type="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('password') border-red-500 @enderror" 
                               placeholder="Enter Password">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setupPinInputs();
        document.querySelector('[data-pin-index="0"]').focus();
    });

    function setupPinInputs() {
        const inputs = document.querySelectorAll('[data-pin-index]');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    } else {
                        setTimeout(() => {
                            const allFilled = Array.from(inputs).every(input => input.value.length === 1);
                            if (allFilled) {
                                verifyPin();
                            }
                        }, 100);
                    }
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
    }

    function verifyPin() {
        const inputs = document.querySelectorAll('[data-pin-index]');
        const pin = Array.from(inputs).map(input => input.value).join('');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch('{{ route('verify.pin') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('pin-overlay').classList.add('hidden');
                document.getElementById('login-content').classList.remove('hidden');
            } else {
                showResult(data.message || 'Invalid PIN. Please try again.', 'error');
                inputs.forEach(input => input.value = '');
                inputs[0].focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult(error.message || 'An error occurred. Please try again.', 'error');
            inputs.forEach(input => input.value = '');
            inputs[0].focus();
        });
    }

    function showResult(message, type) {
        const resultDiv = document.getElementById('result');
        resultDiv.textContent = message;
        resultDiv.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
        
        if (type === 'success') {
            resultDiv.classList.add('bg-green-100');
            resultDiv.classList.add('text-green-800');
        } else {
            resultDiv.classList.add('bg-red-100');
            resultDiv.classList.add('text-red-800');
        }
        
        resultDiv.classList.remove('hidden');
    }
    </script>
</x-layout.master>