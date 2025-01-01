<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Facial Recognition Attendance</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center mb-6">Facial Recognition Attendance</h1>
            
            <div id="videoFeed" class="w-full aspect-video bg-gray-200 mb-4 relative">
                <video id="video" width="720" height="560" autoplay muted></video>
                <canvas id="canvas" class="absolute top-0 left-0" width="720" height="560"></canvas>
            </div>

            <div id="status" class="text-center text-lg mb-4 p-2">
                Loading model...
            </div>

            <div class="text-center space-x-4">
                <button id="clockInButton" 
                        class="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
                        disabled>
                    Clock In
                </button>
                <button id="clockOutButton" 
                        class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
                        disabled>
                    Clock Out
                </button>
            </div>

            <div id="lastAction" class="mt-4 text-center text-sm text-gray-600">
                <!-- Last action will be displayed here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/human@latest/dist/human.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const status = document.getElementById('status');
        const clockInButton = document.getElementById('clockInButton');
        const clockOutButton = document.getElementById('clockOutButton');
        const lastAction = document.getElementById('lastAction');
        
        let human;
        let detectionInterval;
        let faceDetected = false;

        // Initialize Human library
        async function initHuman() {
            try {
                human = new Human({
                    modelBasePath: 'https://cdn.jsdelivr.net/npm/@vladmandic/human/models/',
                    face: {
                        enabled: true,
                        detector: { rotation: false },
                        mesh: { enabled: false },
                        iris: { enabled: false },
                        description: { enabled: false },
                        emotion: { enabled: false }
                    },
                    body: { enabled: false },
                    hand: { enabled: false },
                    object: { enabled: false },
                    gesture: { enabled: false }
                });

                await human.load();
                status.textContent = 'Model loaded. Please position your face in the camera.';
                startDetection();
            } catch (error) {
                console.error('Error initializing Human:', error);
                status.textContent = 'Error loading model. Please refresh the page.';
            }
        }

        // Start camera
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 720,
                        height: 560,
                        facingMode: 'user'
                    } 
                });
                video.srcObject = stream;
                await video.play();
            } catch (error) {
                console.error('Error accessing camera:', error);
                status.textContent = 'Error accessing camera. Please check permissions.';
            }
        }

        // Face detection loop
        async function startDetection() {
            if (detectionInterval) clearInterval(detectionInterval);

            detectionInterval = setInterval(async () => {
                const result = await human.detect(video);
                
                // Clear previous drawings
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                if (result.face && result.face.length > 0) {
                    // Draw face detection box
                    human.draw.face(canvas, result.face[0]);
                    
                    if (!faceDetected) {
                        faceDetected = true;
                        status.textContent = 'Face detected! You can now clock in/out.';
                        clockInButton.disabled = false;
                        clockOutButton.disabled = false;
                    }
                } else {
                    if (faceDetected) {
                        faceDetected = false;
                        status.textContent = 'No face detected. Please position your face in the camera.';
                        clockInButton.disabled = true;
                        clockOutButton.disabled = true;
                    }
                }
            }, 100);
        }

        // Handle clock in/out
        async function handleAttendance(type) {
            try {
                const response = await fetch('/attendance/record', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ type })
                });

                const data = await response.json();
                
                if (data.success) {
                    status.textContent = `Successfully ${type === 'in' ? 'clocked in' : 'clocked out'}!`;
                    lastAction.textContent = `Last action: ${type === 'in' ? 'Clock In' : 'Clock Out'} at ${new Date().toLocaleTimeString()}`;
                } else {
                    status.textContent = data.message || 'Error recording attendance';
                }
            } catch (error) {
                console.error('Error:', error);
                status.textContent = 'Error recording attendance. Please try again.';
            }
        }

        // Event listeners
        clockInButton.addEventListener('click', () => handleAttendance('in'));
        clockOutButton.addEventListener('click', () => handleAttendance('out'));

        // Initialize
        startCamera();
        initHuman();
    </script>
</body>
</html> 