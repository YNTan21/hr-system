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
            
            <div class="mb-4">
                <select id="userSelect" class="w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mx-auto block">
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                        <option value="{{ $user->username }}">{{ $user->username }}</option>
                    @endforeach
                </select>
            </div>
            
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
        let isProcessing = false;

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

        // 添加一个获取 CSRF token 的函数
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        // 优化打卡处理函数
        async function handleAttendance(type) {
            if (isProcessing) {
                console.log('Request in progress, please wait...');
                return;
            }

            try {
                isProcessing = true;
                
                const button = type === 'in' ? 
                    document.getElementById('clockInButton') : 
                    document.getElementById('clockOutButton');
                
                if (button) {
                    button.classList.add('processing');
                    button.disabled = true;
                }

                const username = document.getElementById('userSelect').value;
                if (!username) {
                    showNotification('Please select an employee first', 'error');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                // 格式化当前时间为 HH:mm:ss 格式
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const formattedTime = `${hours}:${minutes}:${seconds}`;

                console.log('Sending attendance request:', { type, username, local_time: formattedTime });

                const response = await fetch('/attendance/record', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type,
                        username: username,
                        local_time: formattedTime
                    })
                });

                const data = await response.json();
                console.log('Attendance response:', data);
                
                if (data.success) {
                    const message = type === 'in' 
                        ? `Clock in successful at ${formattedTime}`
                        : `Clock out successful at ${formattedTime}`;
                        
                    showNotification(message, 'success');
                    
                    // 更新按钮状态
                    updateButtonStates(data.data.canClockIn, data.data.canClockOut);
                } else {
                    showNotification(data.message, 'error');
                    if (data.lastClockIn) {
                        showNotification(`You already clocked in at ${data.lastClockIn}. Please clock out first.`, 'warning');
                    }
                }
            } catch (error) {
                console.error('Attendance error:', error);
                showNotification('Error recording attendance. Please try again.', 'error');
            } finally {
                isProcessing = false;
                const button = type === 'in' ? 
                    document.getElementById('clockInButton') : 
                    document.getElementById('clockOutButton');
                if (button) {
                    button.classList.remove('processing');
                }
            }
        }

        function updateButtonStates(canClockIn, canClockOut) {
            const clockInButton = document.getElementById('clockInButton');
            const clockOutButton = document.getElementById('clockOutButton');
            
            if (clockInButton && clockOutButton) {
                clockInButton.disabled = !canClockIn;
                clockOutButton.disabled = !canClockOut;
            }
        }

        // 添加通知函数
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 4px;
                color: white;
                font-weight: 500;
                z-index: 1000;
                animation: slideIn 0.5s ease-out;
            `;

            // 根据类型设置背景色
            switch (type) {
                case 'success':
                    notification.style.backgroundColor = '#4CAF50';
                    break;
                case 'error':
                    notification.style.backgroundColor = '#f44336';
                    break;
                default:
                    notification.style.backgroundColor = '#2196F3';
            }

            notification.textContent = message;
            document.body.appendChild(notification);

            // 3秒后自动消失
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.5s ease-out';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }

        // 添加动画样式
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }

            .notification {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .notification.success {
                border-left: 4px solid #45a049;
            }

            .notification.error {
                border-left: 4px solid #d32f2f;
            }

            .notification.info {
                border-left: 4px solid #1976d2;
            }

            .processing {
                opacity: 0.7;
                cursor: not-allowed;
                position: relative;
            }

            .processing::after {
                content: '';
                position: absolute;
                width: 16px;
                height: 16px;
                top: 50%;
                left: 50%;
                margin: -8px 0 0 -8px;
                border: 2px solid transparent;
                border-top-color: #ffffff;
                border-radius: 50%;
                animation: button-loading-spinner 0.6s linear infinite;
            }

            @keyframes button-loading-spinner {
                from {
                    transform: rotate(0turn);
                }
                to {
                    transform: rotate(1turn);
                }
            }

            .notification.warning {
                background-color: #ff9800;
                border-left: 4px solid #f57c00;
            }
        `;
        document.head.appendChild(style);

        // 添加按钮点击事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const clockInButton = document.getElementById('clockInButton');
            const clockOutButton = document.getElementById('clockOutButton');

            if (clockInButton) {
                clockInButton.addEventListener('click', () => {
                    if (!isProcessing) {
                        handleAttendance('in');
                    }
                });
            }

            if (clockOutButton) {
                clockOutButton.addEventListener('click', () => {
                    if (!isProcessing) {
                        handleAttendance('out');
                    }
                });
            }
        });

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
                const username = document.getElementById('userSelect').value;
                if (!username) {
                    status.textContent = 'Please select an employee first';
                    return;
                }

                console.log('Sending verification request for user:', username); // 调试日志

                const response = await fetch('/attendance/verify-face', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        descriptor: Array.from(faceDescriptor),
                        username: username
                    })
                });

                // 添加响应状态检查
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server response:', text); // 显示完整的错误响应
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Verification response:', data); // 调试日志
                
                if (data.verified) {
                    status.textContent = 'Face verified! You can now clock in/out.';
                    clockInButton.disabled = !data.canClockIn;
                    clockOutButton.disabled = !data.canClockOut;
                } else {
                    status.textContent = `Face not recognized: ${data.message}`;
                    clockInButton.disabled = true;
                    clockOutButton.disabled = true;
                }
            } catch (error) {
                console.error('Verification error details:', error); // 详细错误信息
                status.textContent = 'Error verifying face. Please try again.';
                clockInButton.disabled = true;
                clockOutButton.disabled = true;
            }
        }

        // 初始化
        startCamera();
        initFaceAPI();

        // 初始化时检查最后的打卡状态
        async function checkLastAttendanceStatus() {
            const username = document.getElementById('userSelect').value;
            if (!username) return;

            try {
                const response = await fetch(`/attendance/last-status/${username}`);
                const data = await response.json();
                updateButtonStates(data.canClockIn, data.canClockOut);
            } catch (error) {
                console.error('Error checking last attendance status:', error);
            }
        }

        // 在页面加载和选择用户时检查状态
        document.addEventListener('DOMContentLoaded', function() {
            const userSelect = document.getElementById('userSelect');
            if (userSelect) {
                userSelect.addEventListener('change', checkLastAttendanceStatus);
                checkLastAttendanceStatus();
            }
        });
    </script>
</body>
</html> 