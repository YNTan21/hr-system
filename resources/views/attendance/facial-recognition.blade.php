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

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const status = document.getElementById('status');
        const clockInButton = document.getElementById('clockInButton');
        const clockOutButton = document.getElementById('clockOutButton');
        const lastAction = document.getElementById('lastAction');
        
        let detectionInterval;
        let faceDetected = false;
        let isClockedIn = false;

        // 初始化 face-api.js
        async function initFaceAPI() {
            try {
                await Promise.all([
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.ssdMobilenetv1.loadFromUri('/models')
                ]);
                
                status.textContent = 'Model loaded. Please position your face in the camera.';
                startDetection();
            } catch (error) {
                console.error('Error initializing face-api:', error);
                status.textContent = 'Error loading model. Please refresh the page.';
            }
        }

        // 修改人脸检测循环
        async function startDetection() {
            if (detectionInterval) clearInterval(detectionInterval);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video)
                    .withFaceLandmarks()
                    .withFaceDescriptors();
                
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                if (detections.length > 0) {
                    const detection = detections[0];
                    // 绘制人脸检测框
                    const dims = faceapi.matchDimensions(canvas, video, true);
                    const resizedDetections = faceapi.resizeResults(detections, dims);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    
                    // 发送面部特征向量到服务器进行验证
                    verifyFace(detection.descriptor);
                    
                    if (!faceDetected) {
                        faceDetected = true;
                        status.textContent = 'Verifying face...';
                        clockInButton.disabled = true;
                        clockOutButton.disabled = true;
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

        // 修改考勤记录函数
        async function handleAttendance(type) {
            try {
                const detections = await faceapi.detectAllFaces(video)
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                if (detections.length === 0) {
                    status.textContent = 'No face detected. Please position your face in the camera.';
                    return;
                }

                const faceEmbedding = result.face[0].embedding;
                
                const response = await fetch('/attendance/record', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        type,
                        descriptor: Array.from(faceDescriptor) // 转换为普通数组以便JSON序列化
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    status.textContent = `Successfully ${type === 'in' ? 'clocked in' : 'clocked out'}!`;
                    lastAction.textContent = `Last action: ${type === 'in' ? 'Clock In' : 'Clock Out'} at ${new Date().toLocaleTimeString()}`;
                    
                    // 更新按钮状态
                    isClockedIn = type === 'in';
                    clockInButton.disabled = type === 'in';
                    clockOutButton.disabled = type === 'out';
                } else {
                    status.textContent = data.message || 'Error recording attendance';
                }
            } catch (error) {
                console.error('Error:', error);
                status.textContent = 'Error recording attendance. Please try again.';
            }
        }

        // 修改事件监听器
        clockInButton.addEventListener('click', () => handleAttendance('in'));
        clockOutButton.addEventListener('click', () => handleAttendance('out'));

        // 添加 startCamera 函数
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

        // 添加面部验证函数
        async function verifyFace(faceDescriptor) {
            try {
                const response = await fetch('/attendance/verify-face', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        descriptor: Array.from(faceDescriptor)
                    })
                });

                const data = await response.json();
                console.log('Verification response:', data); // 添加调试日志
                
                if (data.verified) {
                    status.textContent = 'Face verified! You can now clock in/out.';
                    clockInButton.disabled = !data.canClockIn;
                    clockOutButton.disabled = !data.canClockOut;
                } else {
                    status.textContent = `Face not recognized: ${data.message}`; // 显示详细错误信息
                    clockInButton.disabled = true;
                    clockOutButton.disabled = true;
                }
            } catch (error) {
                console.error('Error verifying face:', error);
                status.textContent = 'Error verifying face. Please try again.';
            }
        }

        // 初始化
        startCamera();
        initFaceAPI();
    </script>
</body>
</html> 