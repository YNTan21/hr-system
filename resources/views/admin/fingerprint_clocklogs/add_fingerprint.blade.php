@section('site-title', 'Add Fingerprint')
<x-layout.master>
    <div class="container-fluid">
        <div class="row">
            <x-sharedata.header></x-sharedata.header>
        </div>
        <!-- Main Content -->
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-2xl mb-4">Add New Fingerprint</h1>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <form id="fingerprintForm" action="{{ route('admin.fingerprint_clocklogs.store_fingerprint') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="user_id" class="block text-gray-700 font-bold mb-2">Select Employee:</label>
                            <select id="user_id" name="user_id" class="w-full px-3 py-2 border rounded-lg" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                            <label class="block text-gray-700 font-bold mb-2">Fingerprint Registration:</label>
                            
                            <div class="flex gap-4 mb-4">
                                <div class="w-1/2">
                                    <div class="border rounded-lg p-4 bg-white">
                                        <h3 class="text-sm font-semibold mb-2">Live Preview</h3>
                                        <canvas id="fingerprintPreview" width="300" height="400" class="border"></canvas>
                                        <div class="mt-2 text-sm text-gray-500" id="qualityScore">Quality: --</div>
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <div class="border rounded-lg p-4 bg-white">
                                        <h3 class="text-sm font-semibold mb-2">Scan Status</h3>
                                        <div id="scanStatus" class="mb-3 text-gray-600">Select an employee and click 'Start Scan'</div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="fingerprint_id" name="fingerprint_id">
                            <div class="flex gap-2">
                                <button type="button" id="scanButton" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-400">
                                    Start Scan
                                </button>
                                <button type="submit" id="submitButton" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:bg-gray-400" disabled>
                                    Save Fingerprint
                                </button>
                            </div>
                            @error('fingerprint_id')
                                <div class="text-red-500 mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                    
                    @if(session('success'))
                        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout.master>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        const scanButton = document.getElementById('scanButton');
        const submitButton = document.getElementById('submitButton');
        const scanStatus = document.getElementById('scanStatus');
        const fingerprintInput = document.getElementById('fingerprint_id');
        const canvas = document.getElementById('fingerprintPreview');
        const ctx = canvas.getContext('2d');
        const qualityScore = document.getElementById('qualityScore');

        // Initialize canvas
        function resetCanvas() {
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.font = '14px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText('No fingerprint detected', canvas.width / 2, canvas.height / 2);
            qualityScore.textContent = 'Quality: --';
        }
        resetCanvas();

        async function checkScannerConnection() {
            try {
                const response = await fetch('/api/check-scanner-status', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();
                return result.isConnected;
            } catch (error) {
                console.error('Scanner check error:', error);
                return false;
            }
        }

        async function scanFingerprint() {
            if (!userSelect.value) {
                scanStatus.textContent = 'Please select an employee first';
                scanStatus.className = 'mb-3 text-red-600';
                return;
            }

            try {
                scanButton.disabled = true;
                scanStatus.textContent = 'Checking scanner connection...';
                scanStatus.className = 'mb-3 text-blue-600';

                // Check scanner connection first
                const isConnected = await checkScannerConnection();
                if (!isConnected) {
                    throw new Error('Fingerprint scanner is not connected. Please connect the scanner and try again.');
                }

                scanStatus.textContent = 'Initializing scanner...';

                const response = await fetch('/api/scan-fingerprint', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        user_id: userSelect.value
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();
                
                if (result.success) {
                    // Display the captured fingerprint image
                    const img = new Image();
                    img.onload = () => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    };
                    img.src = `data:image/png;base64,${result.image}`;

                    // Update quality score and fingerprint ID
                    qualityScore.textContent = `Quality: ${result.quality}%`;
                    fingerprintInput.value = result.fingerprint_id;

                    // Enable submit button if quality is acceptable
                    if (result.quality >= 60) {
                        submitButton.disabled = false;
                        scanStatus.textContent = 'Fingerprint captured successfully!';
                        scanStatus.className = 'mb-3 text-green-600';
                    } else {
                        submitButton.disabled = true;
                        scanStatus.textContent = 'Poor quality scan. Please try again.';
                        scanStatus.className = 'mb-3 text-yellow-600';
                    }
                } else {
                    throw new Error(result.message || 'Failed to capture fingerprint');
                }
            } catch (error) {
                console.error('Scan error:', error);
                scanStatus.textContent = `Error: ${error.message}`;
                scanStatus.className = 'mb-3 text-red-600';
                resetCanvas();
                
                // Show a more user-friendly error message for common issues
                if (error.message.includes('not connected')) {
                    // Add a visual indicator for scanner disconnection
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mt-2 p-2 bg-red-100 text-red-700 rounded';
                    errorDiv.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>Scanner not detected. Please check the connection.</span>
                        </div>
                    `;
                    scanStatus.appendChild(errorDiv);
                }
            } finally {
                scanButton.disabled = false;
            }
        }

        // Event Listeners
        scanButton.addEventListener('click', scanFingerprint);
        
        userSelect.addEventListener('change', function() {
            resetCanvas();
            submitButton.disabled = true;
            fingerprintInput.value = '';
            scanStatus.textContent = this.value ? 'Ready to scan' : 'Select an employee first';
            scanStatus.className = 'mb-3 text-gray-600';
        });

        // Initial scanner check
        checkScannerConnection().then(isConnected => {
            if (!isConnected) {
                scanStatus.textContent = 'Scanner not connected';
                scanStatus.className = 'mb-3 text-red-600';
                scanButton.disabled = true;
            }
        });
    });
</script>
