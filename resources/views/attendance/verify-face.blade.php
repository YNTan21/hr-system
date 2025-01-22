<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Face</title>
    @vite(['resources/css/app.css'])
    <script src="{{ asset('facejs/face-api.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="mainContent" class="">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">
                <!-- Add Home Button -->
                <div class="mb-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                </div>

                <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Register Your Face</h1>

                <div class="flex gap-8">
                    <!-- Left Column - Employee Info -->
                    <div class="flex-1 space-y-6">
                        <!-- Employee Selection -->
                        <div class="mb-6">
                            <label for="userSelect" class="block text-sm font-semibold text-gray-700 mb-2">Employee Name</label>
                            <select id="userSelect" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                                <option value="">Choose an employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->username }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Register Face Button -->
                        <div class="mb-6">
                            <button id="registerFaceButton" 
                                    class="w-full flex items-center justify-center p-4 bg-white rounded-lg border-2 border-blue-500 text-blue-700 hover:bg-blue-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="font-medium">Register Face</span>
                            </button>
                        </div>

                        <!-- Status Display -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div id="status" class="text-gray-700">
                                Position your face in the camera. Click "Register Face" to proceed.
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Video Feed -->
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 h-full transition-all hover:border-blue-500">
                            <div class="flex flex-col items-center space-y-4 h-full">
                                <div id="videoFeed" class="w-full h-64 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200 mb-4 overflow-hidden relative">
                                    <video id="video" class="w-full h-full object-cover" autoplay muted></video>
                                    <canvas id="canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const status = document.getElementById('status');
        const registerFaceButton = document.getElementById('registerFaceButton');

        // Add debug status display
        function updateStatus(message) {
            status.textContent = message;
            console.log('Status updated:', message);
        }

        // Modified loadModels function with better error handling
        async function loadModels() {
            try {
                updateStatus('Loading models...');
                const MODEL_URL = '/models';
                await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
                updateStatus('Face detection model loaded');
                
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                updateStatus('Landmark detection model loaded');
                
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                updateStatus('Face recognition model loaded');
                
                updateStatus('All models loaded successfully');
                startFaceDetection(); // Add continuous face detection
            } catch (error) {
                console.error('Error loading models:', error);
                updateStatus('Error loading models. Check console for details.');
            }
        }

        // Add continuous face detection
        async function startFaceDetection() {
            setInterval(async () => {
                const detections = await faceapi.detectSingleFace(video)
                    .withFaceLandmarks();
                
                if (detections) {
                    // Clear previous drawings
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    // Draw the detections
                    const displaySize = { width: video.width, height: video.height };
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                }
            }, 100);
        }

        // Modified startCamera function with error details
        async function startCamera() {
            try {
                updateStatus('Starting camera...');
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 720,
                        height: 560,
                        facingMode: 'user'
                    } 
                });
                video.srcObject = stream;
                await video.play();
                updateStatus('Camera started successfully');
            } catch (error) {
                console.error('Camera error:', error);
                updateStatus(`Camera error: ${error.message}`);
            }
        }

        // 注册人脸
        async function registerFace() {
            try {
                // First check if face is detected
                const detections = await faceapi.detectSingleFace(video)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detections) {
                    updateStatus('No face detected! Please position your face in the camera.');
                    return;
                }

                // Get username from dropdown
                const username = document.getElementById('userSelect').value;
                if (!username) {
                    updateStatus('Please select a user first.');
                    return;
                }

                updateStatus('Processing registration...');
                const faceDescriptor = Array.from(detections.descriptor);

                // Send data to server
                const response = await fetch('/attendance/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        username: username,
                        faceDescriptor: faceDescriptor
                    })
                });

                const data = await response.json();
                console.log('Registration response:', data);
                
                if (data.success) {
                    updateStatus(`Face registered successfully! (${data.descriptorCount}/${data.maxDescriptors} registrations used)`);
                } else {
                    updateStatus(data.message || 'Registration failed. Please try again.');
                }
            } catch (error) {
                console.error('Registration error:', error);
                updateStatus('Error during registration. Please try again.');
            }
        }

        // 初始化过程
        document.addEventListener('DOMContentLoaded', async () => {
            await loadModels(); // 加载Face API模型
            await startCamera();
        });

        // 注册按钮点击事件
        registerFaceButton.addEventListener('click', registerFace);
    </script>
</body>
</html>
