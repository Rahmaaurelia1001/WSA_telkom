<!-- Create a new file: resources/views/components/clock-notification.blade.php -->
<div>
    <!-- Clock -->
    <div class="clock-container">
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
            <button id="saveNotificationTime" class="save-button">Simpan</button>
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

    <style>
        .clock-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 9999;
            min-width: 180px;
        }

        .notification {
            display: none;
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 10px;
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 9998;
        }

        #current-time {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1a1a1a;
        }

        #countdown {
            font-size: 1rem;
            color: #4b5563;
            margin-top: 5px;
        }

        .notification-settings {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 220px;
            background-color: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        .notification-input {
            width: 60px;
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-align: center;
            margin-right: 6px;
        }

        .save-button {
            background-color: #dc2626;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .save-button:hover {
            background-color: #b91c1c;
        }

        .atur-button {
            background-color: #b91c13;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 8px;
        }

        .atur-button:hover {
            background-color: #b91c13;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeDisplay = document.getElementById('current-time');
            const countdownDisplay = document.getElementById('countdown');
            const notification = document.getElementById('notification');
            const notificationMinuteInput = document.getElementById('notificationMinute');
            const saveNotificationTimeBtn = document.getElementById('saveNotificationTime');
            const aturButton = document.getElementById('aturButton');
            const notificationSettings = document.getElementById('notificationSettings');

            let notificationMinute = parseInt(localStorage.getItem('notificationMinute')) || 55;
            notificationMinuteInput.value = notificationMinute;

            if (Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        console.log('Notification permission granted.');
                    } else {
                        console.log('Notification permission denied.');
                    }
                });
            }

            aturButton.addEventListener('click', function() {
                notificationSettings.style.display = 'block';
            });

            saveNotificationTimeBtn.addEventListener('click', function() {
                const newMinute = parseInt(notificationMinuteInput.value);
                if (newMinute >= 0 && newMinute <= 59) {
                    notificationMinute = newMinute;
                    localStorage.setItem('notificationMinute', notificationMinute);
                    alert(`Notifikasi akan muncul pada menit ke-${notificationMinute}`);
                    notificationSettings.style.display = 'none';
                } else {
                    alert('Masukkan angka antara 0-59');
                    notificationMinuteInput.value = notificationMinute;
                }
            });

            function showDesktopNotification() {
                if (Notification.permission === 'granted') {
                    new Notification('Waktunya bersiap untuk merekap data!', {
                        body: 'Notifikasi muncul pada menit ke-' + notificationMinute,
                        icon: '/images/logo-telkom.png'
                    });
                } else {
                    console.log('Notification permission not granted.');
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

                if (now.getMinutes() === notificationMinute && now.getSeconds() === 0) {
                    notification.style.display = 'block';
                    showDesktopNotification();
                    setTimeout(() => { notification.style.display = 'none'; }, 20000);
                }
            }

            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
</div>