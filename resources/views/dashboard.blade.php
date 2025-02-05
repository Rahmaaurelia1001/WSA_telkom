<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .clock-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 9999;
            min-width: 200px;
        }

        .notification {
            display: none;
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 9998;
        }

        #current-time {
            font-size: 2rem;
            font-weight: bold;
            color: #1a1a1a;
        }

        #countdown {
            font-size: 1rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }

        .notification-settings {
            display: none; /* Sembunyikan form input dan tombol simpan secara default */
            position: fixed;
            bottom: 20px;
            right: 300px;
            background-color: white;
            padding: 16px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        .notification-input {
            width: 80px;
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin-right: 8px;
        }

        .save-button {
            background-color: #dc2626;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .save-button:hover {
            background-color: #b91c1c;
        }

        .atur-button {
            background-color: #4b5563;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .atur-button:hover {
            background-color: #374151;
        }
    </style>
</head>

<body class="bg-gray-100">
    @include('navbar')

    <!-- Clock -->
    <div class="clock-container" id="clockContainer">
        <div id="current-time"></div>
        <div id="countdown"></div>
        <button id="aturButton" class="atur-button">Atur</button>
    </div>

    <!-- Notification Settings -->
    <div class="notification-settings" id="notificationSettings">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Atur notifikasi muncul pada menit ke-
        </label>
        <div class="flex items-center">
            <input 
                type="number" 
                id="notificationMinute" 
                min="0" 
                max="59" 
                class="notification-input"
                value="55"
            >
            <button 
                id="saveNotificationTime" 
                class="save-button"
            >
                Simpan
            </button>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span>Waktunya bersiap untuk merekap data!</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col items-center justify-center min-h-screen space-y-4">
        <div class="flex items-center space-x-8" style="margin-top: -80px;">
            <h1 class="text-3xl font-bold mt-6 mb-6">
                Welcome to <br>
                <span class="text-red-600 text-4xl">Dashboard Page</span>
            </h1>
            <img src="/images/logo-telkom.png" alt="Telkom Indonesia" style="width: 200px; margin-top: -45px;">
        </div>

        <a href="{{ route('upload.form') }}" class="mt-4">
            <div class="border-4 border-red-600 rounded-lg p-6 bg-white shadow-md flex flex-col items-center space-y-2 cursor-pointer">
                <img src="/images/assurance.png" alt="WSA" class="h-32">
                <span class="text-black-600 text-lg">WSA</span>
            </div>
        </a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeDisplay = document.getElementById('current-time');
        const countdownDisplay = document.getElementById('countdown');
        const notification = document.getElementById('notification');
        const notificationMinuteInput = document.getElementById('notificationMinute');
        const saveNotificationTimeBtn = document.getElementById('saveNotificationTime');
        const aturButton = document.getElementById('aturButton');
        const notificationSettings = document.getElementById('notificationSettings');

        // Get saved notification minute or use default
        let notificationMinute = parseInt(localStorage.getItem('notificationMinute')) || 55;
        notificationMinuteInput.value = notificationMinute;

        // Show notification settings when "Atur" button is clicked
        aturButton.addEventListener('click', function() {
            notificationSettings.style.display = 'block';
        });

        // Save notification time
        saveNotificationTimeBtn.addEventListener('click', function() {
            const newMinute = parseInt(notificationMinuteInput.value);
            if (newMinute >= 0 && newMinute <= 59) {
                notificationMinute = newMinute;
                localStorage.setItem('notificationMinute', notificationMinute);
                alert(`Notifikasi akan muncul pada menit ke-${notificationMinute}`);
                
                // Hide the notification settings form
                notificationSettings.style.display = 'none';
            } else {
                alert('Masukkan angka antara 0-59');
                notificationMinuteInput.value = notificationMinute;
            }
        });

        // Request notification permission
        if ("Notification" in window) {
            Notification.requestPermission().then(function(permission) {
                console.log("Notification permission:", permission);
            });
        }

        function showDesktopNotification() {
            try {
                if (!("Notification" in window)) {
                    console.error("Browser ini tidak mendukung desktop notification");
                    return;
                }

                if (Notification.permission === "granted") {
                    const notif = new Notification("Rekap Data Reminder", {
                        body: "Waktunya bersiap untuk merekap data!",
                        icon: "/images/logo-telkom.png",
                        badge: "/images/logo-telkom.png",
                        requireInteraction: true,
                        vibrate: [200, 100, 200],
                        tag: "rekapData"
                    });

                    notif.onclick = function() {
                        window.focus();
                        notif.close();
                    };

                    setTimeout(() => notif.close(), 20000);
                    
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === "granted") {
                            showDesktopNotification();
                        }
                    });
                }
            } catch (error) {
                console.error("Error showing notification:", error);
            }
        }

        function updateClock() {
            const now = new Date();
            
            timeDisplay.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const nextHour = new Date(now);
            nextHour.setHours(now.getHours() + 1);
            nextHour.setMinutes(0);
            nextHour.setSeconds(0);
            nextHour.setMilliseconds(0);

            const diff = nextHour - now;
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            countdownDisplay.textContent = `${minutes} menit ${seconds} detik menuju ${nextHour.getHours().toString().padStart(2, '0')}:00`;

            // Show notification at configured minute
            if (now.getMinutes() === notificationMinute && now.getSeconds() === 0) {
                notification.style.display = 'block';
                showDesktopNotification();
                
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 20000);
            }
        }

        // Initialize clock
        updateClock();
        setInterval(updateClock, 1000);
    });
    </script>
</body>
</html>