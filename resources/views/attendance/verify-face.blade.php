<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Face</title>
    @vite(['resources/css/app.css'])
    <script src="{{ asset('facejs/face-api.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs/dist/tf.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center mb-6">Register Your Face</h1>

            <div id="videoFeed" class="w-full aspect-video bg-gray-200 mb-4 relative">
                <video id="video" width="720" height="560" autoplay muted></video>
                <canvas id="canvas" class="absolute top-0 left-0" width="720" height="560"></canvas>
            </div>

            <div id="status" class="text-center text-lg mb-4 p-2">
                Position your face in the camera. Click "Register Face" to proceed.
            </div>

            <div class="text-center">
                <button id="registerFaceButton" 
                        class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Register Face
                </button>
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
            console.log(message);
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

                // Get username with validation
                const username = prompt("Please enter your Username:");
                if (!username) {
                    updateStatus('Registration cancelled - Username is required.');
                    return;
                }

                if (username.trim() === '') {
                    updateStatus('Username cannot be empty.');
                    return;
                }

                updateStatus('Processing registration...');
                const faceDescriptor = Array.from(detections.descriptor);

                // Send data to server with username instead of id
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
                
                if (data.success) {
                    updateStatus('Face registered successfully!');
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