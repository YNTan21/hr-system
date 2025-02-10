<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fingerprint Verification</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- PIN Modal -->
    <!-- <div id="pinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
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
    </div> -->

    <!-- Main Content (Initially Hidden) -->
    <div id="mainContent" class="">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">
                <!-- Home Button with Auth Check -->
                <div class="mb-4">
                    <a href="{{ Auth::check() ? route('fingerprint.index') : route('login') }}" 
                       class="inline-flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                </div>

                <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Fingerprint Verification</h1>

                <!-- Display Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert">
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('verify.fingerprint') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="flex gap-8">
                        <!-- Left Column - Employee Info -->
                        <div class="flex-1 space-y-6">
                            <!-- Employee Selection -->
                            <div class="mb-6">
                                <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">Employee Name</label>
                                <select name="user_id" 
                                        id="userSelect" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                                    <option value="">Choose an employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Clock Type Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Action</label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-4 bg-white rounded-lg border-2 border-green-500 cursor-pointer hover:bg-green-50 transition-colors">
                                        <input type="radio" name="clock_type" value="in" class="form-radio text-green-600 h-5 w-5" checked>
                                        <span class="ml-2 text-green-700 font-medium">Clock In</span>
                                    </label>
                                    <label class="flex items-center p-4 bg-white rounded-lg border-2 border-red-500 cursor-pointer hover:bg-red-50 transition-colors">
                                        <input type="radio" name="clock_type" value="out" class="form-radio text-red-600 h-5 w-5">
                                        <span class="ml-2 text-red-700 font-medium">Clock Out</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Current Time Display -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <div class="text-4xl font-bold text-gray-800 mb-2" id="currentTime">00:00:00</div>
                                <div class="text-gray-500" id="currentDate"></div>
                            </div>
                        </div>

                        <!-- Right Column - Fingerprint Upload -->
                        <div class="flex-1">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 h-full transition-all hover:border-blue-500">
                                <div class="flex flex-col items-center space-y-4 h-full">
                                    <!-- Preview Area -->
                                    <div id="fingerprintPreview" class="w-full h-64 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200 mb-4">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">No fingerprint uploaded</p>
                                        </div>
                                    </div>

                                    <!-- Upload Button -->
                                    <input type="file" 
                                           id="fingerprint" 
                                           name="fingerprint" 
                                           class="hidden" 
                                           accept="image/*"
                                           onchange="previewFingerprint(this)">
                                    <label for="fingerprint" 
                                           class="px-6 py-3 bg-blue-600 text-white rounded-lg cursor-pointer hover:bg-blue-700 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Upload Fingerprint
                                    </label>

                                    <!-- Submit Button -->
                                    <button type="submit" 
                                            class="w-full px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-medium">
                                        Verify Fingerprint
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // document.getElementById('pinModal').classList.remove('hidden');
        // setupPinInputs();
        startTimeUpdate(); // Start time update immediately
    });

    /* function setupPinInputs() {
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

    function checkPin() {
        const inputs = document.querySelectorAll('[data-pin-index]');
        const pin = Array.from(inputs).map(input => input.value).join('');
        
        if (pin === '000000') {
            document.getElementById('pinModal').classList.add('hidden');
            document.getElementById('mainContent').classList.remove('hidden');
            startTimeUpdate();
        } else {
            alert('Invalid PIN number');
            inputs.forEach(input => input.value = '');
            inputs[0].focus();
        }
    }

    function closePinModal() {
        window.location.href = '{{ route("fingerprint.index") }}';
    } */

    document.querySelector('form').addEventListener('submit', function(e) {
        const pinModal = document.getElementById('pinModal');
        if (!pinModal.classList.contains('hidden')) {
            e.preventDefault();
            alert('Please enter PIN number first');
        }
    });

    // Keep the existing time update functions
    function startTimeUpdate() {
        updateTime();
        setInterval(updateTime, 1000);
    }

    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour12: false, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        const dateString = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        document.getElementById('currentTime').textContent = timeString;
        document.getElementById('currentDate').textContent = dateString;
    }

    function previewFingerprint(input) {
        const preview = document.getElementById('fingerprintPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         class="max-w-full max-h-full object-contain rounded-lg"
                         alt="Fingerprint">
                `;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
