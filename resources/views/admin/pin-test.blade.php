@section('site-title', 'PIN Verification Test')
<x-layout.master>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="main-wrapper min-h-screen bg-gray-100 flex items-center">
        <div class="container mx-auto">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-2">PIN Verification Test</h3>
                </div>

                <!-- PIN Input Section -->
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

                    <!-- Result Display -->
                    <div id="result" class="mt-4 p-3 rounded-md text-center hidden"></div>
                </div>
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

        console.log('Sending PIN:', pin); // Debug log

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
            console.log('Server response:', data); // Debug log
            
            if (data.success) {
                showResult('PIN verified successfully!', 'success');
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