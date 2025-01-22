<x-layout.master>
    <div class="main-wrapper min-h-screen bg-gray-100 flex items-center">
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
                    <a href="{{ route('verify.page') }}" 
                       class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-fingerprint text-xl"></i>
                        <span>Fingerprint</span>
                    </a>
                </div>
            </div>

            <!-- Login Box -->
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-2">Login</h3>
                </div>

                <!-- Login Form -->
                <form action="{{ route('auth.login') }}" method="POST">
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

    <!-- PIN Modal -->
    <div id="pinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold mb-4">Security Check</h2>
            <p class="mb-4">Please enter PIN number to access the system:</p>
            
            <div class="flex justify-center space-x-2 mb-4">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="0">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="1">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="2">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="3">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="4">
                <input type="password" maxlength="1" class="w-10 h-10 text-center border rounded text-xl" data-pin-index="5">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button onclick="checkPin()" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Submit
                </button>
                <button onclick="closePinModal()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('pinModal').classList.remove('hidden');
        setupPinInputs();
    });

    function setupPinInputs() {
        const inputs = document.querySelectorAll('[data-pin-index]');
        
        inputs.forEach((input, index) => {
            // 自动聚焦到下一个输入框
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    const nextInput = inputs[index + 1];
                    if (nextInput) nextInput.focus();
                }
            });

            // 处理退格键
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

    function checkPin() {
        const inputs = document.querySelectorAll('[data-pin-index]');
        const pin = Array.from(inputs).map(input => input.value).join('');
        
        if (pin === '000000') {
            document.getElementById('pinModal').classList.add('hidden');
        } else {
            alert('Invalid PIN number');
            inputs.forEach(input => input.value = '');
            inputs[0].focus();
        }
    }

    function closePinModal() {
        window.location.href = '/';
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const pinModal = document.getElementById('pinModal');
        if (!pinModal.classList.contains('hidden')) {
            e.preventDefault();
            alert('Please enter PIN number first');
        }
    });
    </script>
</x-layout.master>