<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fingerprint Enrollment - {{ $employee->username }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen">
    {{-- PIN Modal --}}
    {{-- <div id="pinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
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
    </div> --}}

    <!-- Main Content -->
    <div id="mainContent" class="block">
        <div class="container mx-auto px-4 h-screen flex items-center">
            <div class="w-full bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Fingerprint Enrollment</h2>

                <!-- Display Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @elseif(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Enrollment Form -->
                <form action="{{ route('enroll.fingerprint') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Top Section - Employee Info -->
                        <div class="flex items-center justify-between">
                            <!-- Employee Selection -->
                            <div class="w-1/3">
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                    Employee Username
                                </label>
                                <select id="username" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" disabled>
                                    <option value="{{ $employee->id }}" selected>{{ $employee->username }}</option>
                                </select>
                                <input type="hidden" name="user_id" value="{{ $employee->id }}">
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-4">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    Enroll Fingerprint
                                </button>
                                <a href="{{ route('fingerprint.index') }}" 
                                   class="px-6 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                    Back
                                </a>
                            </div>
                        </div>

                        <!-- Bottom Section - Fingerprint Templates -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                            <div class="flex justify-between space-x-4">
                                @for($i = 1; $i <= 5; $i++)
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Template {{ $i }}
                                    </label>
                                    
                                    <!-- Preview Area -->
                                    <div id="preview{{ $i }}" 
                                         class="aspect-square mb-2 border rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p class="mt-1 text-xs text-gray-500">No image</p>
                                        </div>
                                    </div>

                                    <!-- Upload Button -->
                                    <input type="file" 
                                           id="fingerprint{{ $i }}" 
                                           name="fingerprint{{ $i }}" 
                                           class="hidden"
                                           accept="image/*"
                                           onchange="previewImage(this, {{ $i }})">
                                    <label for="fingerprint{{ $i }}" 
                                           class="w-full px-2 py-1.5 bg-blue-600 text-white rounded-lg cursor-pointer hover:bg-blue-700 transition-colors flex items-center justify-center text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Upload
                                    </label>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Comment out PIN-related JavaScript
        /*
        document.addEventListener('DOMContentLoaded', function() {
            setupPinInputs();
        });

        function checkPin() {
            const inputs = document.querySelectorAll('[data-pin-index]');
            const pin = Array.from(inputs).map(input => input.value).join('');
            
            if (pin === '000000') {
                document.getElementById('pinModal').classList.add('hidden');
                document.getElementById('mainContent').classList.remove('hidden');
            } else {
                alert('Invalid PIN number');
                inputs.forEach(input => input.value = '');
                inputs[0].focus();
            }
        }

        function setupPinInputs() {
            const inputs = document.querySelectorAll('[data-pin-index]');
            
            inputs.forEach((input, index) => {
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

            inputs[0].focus();
        }

        function closePinModal() {
            window.location.href = '{{ route("fingerprint.index") }}';
        }
        */

        // Keep the existing image preview function
        function previewImage(input, index) {
            const preview = document.getElementById(`preview${index}`);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             class="w-full h-full object-cover"
                             alt="Fingerprint ${index}">
                    `;
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = `<span class="text-gray-400 text-sm">No image</span>`;
            }
        }

        // Optional: Add file name display
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                const label = this.nextElementSibling.querySelector('span');
                label.textContent = fileName || 'Choose File';
            });
        });
    </script>
</body>
</html>
