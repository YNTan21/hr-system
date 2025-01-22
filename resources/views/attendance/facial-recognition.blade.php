<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Facial Recognition</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- PIN Modal -->
    <!-- <div id="pinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
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

    <!-- Main Content -->
    <div id="mainContent" class="">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">
                <!-- Add Home Button -->
                <div class="mb-4 flex items-center gap-2">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="inline-flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="{{ route('attendance.verify-face') }}" 
                       class="inline-flex items-center justify-center px-4 h-10 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <span class="font-medium">Register Face</span>
                    </a>
                </div>

                <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Facial Recognition Attendance</h1>

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

                        <!-- Clock Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Action</label>
                            <div class="space-y-2">
                                <button id="clockInButton" 
                                        class="w-full flex items-center p-4 bg-white rounded-lg border-2 border-green-500 cursor-pointer hover:bg-green-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    <span class="ml-2 text-green-700 font-medium">Clock In</span>
                                </button>
                                <button id="clockOutButton"
                                        class="w-full flex items-center p-4 bg-white rounded-lg border-2 border-red-500 cursor-pointer hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    <span class="ml-2 text-red-700 font-medium">Clock Out</span>
                                </button>
                            </div>
                        </div>

                        <!-- Current Time Display -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="text-4xl font-bold text-gray-800 mb-2" id="currentTime">00:00:00</div>
                            <div class="text-gray-500" id="currentDate"></div>
                        </div>
                    </div>

                    <!-- Right Column - Video Feed -->
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 h-full transition-all hover:border-blue-500">
                            <div class="flex flex-col items-center space-y-4 h-full">
                                <div id="videoFeed" class="w-full h-64 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200 mb-4 overflow-hidden">
                                    <video id="video" class="w-full h-full object-cover" autoplay muted></video>
                                    <canvas id="canvas" class="absolute hidden"></canvas>
                                </div>

                                <div id="status" class="text-center text-sm text-gray-500">
                                    Loading model...
                                </div>

                                <div id="lastAction" class="text-center text-sm text-gray-600">
                                    <!-- Last action will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            // document.getElementById('pinModal').classList.remove('hidden');
            // setupPinInputs();
            startTimeUpdate(); // Added this to ensure time updates start immediately
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
            window.location.href = '/';
        } */

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
    </script>
</body>
</html> 