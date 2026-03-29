<!DOCTYPE html>
<html lang="en" class="{{ isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'theme-light' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Roomie - Expense Splitter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --accent: #f43f5e;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text: #f8fafc;
            --text-dim: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body.theme-light {
            --bg: #f1f5f9;
            --card-bg: rgba(255, 255, 255, 0.8);
            --text: #0f172a;
            --text-dim: #64748b;
            --glass-border: rgba(99, 102, 241, 0.1);
        }

        @media (prefers-color-scheme: light) {
            /* Optional: user might want it to follow system by default */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            overflow-x: hidden;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.15) 0%, transparent 50%);
            min-height: 100vh;
            padding-bottom: 80px;
            /* Space for bottom nav */
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 20px 20px 20px;
        }

        header {
            position: sticky;
            top: 0;
            padding: 16px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 1000;
            margin: 0 -20px 20px -20px;
            padding: 16px 20px;
        }

        .theme-light header {
            background: rgba(241, 245, 249, 0.8);
        }

        .logo {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary-light), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:active {
            transform: scale(0.95);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.75rem;
            gap: 4px;
            transition: color 0.2s;
        }

        .nav-item.active {
            color: var(--primary-light);
        }

        .nav-item i {
            font-size: 1.25rem;
        }

        .fab {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
            z-index: 900;
            text-decoration: none;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .badge-danger {
            background: rgba(244, 63, 94, 0.2);
            color: #fb7185;
        }

        /* Select & Input Fixes */
        select option {
            background-color: #1e293b;
            color: white;
        }

        .theme-light select option {
            background-color: white;
            color: #0f172a;
        }

        /* Custom Modal */
        #custom-confirm-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .modal-message {
            color: var(--text-dim);
            font-size: 0.875rem;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        h1,
        h2,
        h3 {
            margin-bottom: 12px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes splashFadeOut {
            from {
                opacity: 1;
                visibility: visible;
            }

            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        #splash-screen {
            position: fixed;
            inset: 0;
            background: #0f172a;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.8s ease-out;
        }

        .splash-logo {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            letter-spacing: -2px;
            animation: pulse 2s infinite;
        }

        .splash-loader {
            width: 40px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            position: relative;
        }

        .splash-loader-bar {
            position: absolute;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary);
            animation: loaderMove 1.5s infinite;
        }

        @keyframes loaderMove {
            0% {
                left: -100%;
            }

            50% {
                left: 0%;
            }

            100% {
                left: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const splash = document.getElementById('splash-screen');
            if (splash) {
                if (sessionStorage.getItem('splash_shown')) {
                    splash.style.display = 'none';
                    return;
                }

                setTimeout(() => {
                    splash.style.opacity = '0';
                    setTimeout(() => {
                        splash.style.display = 'none';
                        sessionStorage.setItem('splash_shown', 'true');
                    }, 800);
                }, 2000);
            }
        });
    </script>
    <script>
        function toggleTheme() {
            const isLight = document.body.classList.toggle('theme-light');
            document.documentElement.classList.toggle('theme-light');

            // Set cookie for server-side persistence (30 days)
            document.cookie = `theme=${isLight ? 'light' : 'dark'}; path=/; max-age=${60*60*24*30}; SameSite=Lax`;
            localStorage.setItem('theme', isLight ? 'light' : 'dark');

            // Update UI if on settings page
            const text = document.getElementById('theme-status');
            if (text) text.innerText = isLight ? 'Light Mode' : 'Dark Mode';
        }
    </script>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#6366f1">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Register the primary worker (for PWA & Background Messaging)
                // We don't register firebase-messaging-sw.js separately here to avoid conflicts
                navigator.serviceWorker.register('{{ asset('worker.js') }}')
                    .then(function(registration) {
                        console.log('Roomie ServiceWorker registered with scope:', registration.scope);
                    });
            });
        }
    </script>
</head>

<body class="{{ isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'theme-light' : '' }}">
    <div class="container">
        <header>
            <div style="display: flex; flex-direction: column;">
                <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit;">
                    <div class="logo">Roomie</div>
                </a>
                @if (session('active_group_id'))
                    @php $activeGroup = \App\Models\Group::find(session('active_group_id')); @endphp
                    @if ($activeGroup)
                        <a href="{{ route('groups.index') }}" style="text-decoration: none;">
                            <span
                                style="font-size: 0.625rem; font-weight: 700; color: var(--primary-light); background: rgba(99, 102, 241, 0.1); padding: 2px 8px; border-radius: 10px; border: 1px solid rgba(99, 102, 241, 0.2);">
                                <i class="fa-solid fa-layer-group mr-1" style="font-size: 0.5rem;"></i>
                                {{ strtoupper($activeGroup->name) }}
                            </span>
                        </a>
                    @endif
                @endif
            </div>

            @auth
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="text-align: right; display: flex; flex-direction: column; gap: 2px;">
                        <a href="{{ route('profile.edit') }}"
                            style="text-decoration: none; color: inherit; display: block;">
                            <p style="font-size: 0.75rem; font-weight: 700;">{{ Auth::user()->name }}</p>
                        </a>
                        <a href="{{ route('logout') }}"
                            style="font-size: 0.625rem; color: var(--accent); text-decoration: none; display: block;">Logout</a>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="user-pill" style="text-decoration: none;">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}"
                                style="width: 32px; height: 32px; border-radius: 16px; object-fit: cover; border: 1px solid var(--primary-light);">
                        @else
                            <div
                                style="width: 32px; height: 32px; border-radius: 16px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border);">
                                <i class="fa-solid fa-user" style="font-size: 0.875rem; color: var(--text-dim);"></i>
                            </div>
                        @endif
                    </a>
                </div>
            @endauth
        </header>

        @if ($errors->any())
            <div class="card animate-fade-in" style="border-color: var(--accent); background: rgba(244, 63, 94, 0.1);">
                <ul style="color: #fb7185; list-style: none; font-size: 0.875rem; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="card animate-fade-in" style="border-color: #22c55e; background: rgba(34, 197, 94, 0.1);">
                <p style="color: #4ade80;"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="card animate-fade-in"
                style="border-color: var(--secondary); background: rgba(14, 165, 233, 0.1);">
                <p style="color: var(--secondary);"><i class="fa-solid fa-info-circle"></i> {{ session('info') }}</p>
            </div>
        @endif

        @yield('content')
    </div>

    @auth
        <nav class="bottom-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="nav-item {{ Route::is('expenses.index') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Expenses</span>
            </a>
            <a href="{{ route('roommates.index') }}" class="nav-item {{ Route::is('roommates.index') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Roommates</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ Route::is('settings.index') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
        </nav>

        <a href="{{ route('expenses.create') }}" class="fab">
            <i class="fa-solid fa-plus"></i>
        </a>
    @endauth
    <div id="custom-confirm-modal">
        <div class="modal-card">
            <div id="modal-icon" style="font-size: 3rem; color: var(--accent); margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="modal-title" id="confirm-title">Are you sure?</h3>
            <p class="modal-message" id="confirm-message">This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="modal-confirm-btn" class="btn btn-primary"
                    style="background: var(--accent);">Confirm</button>
                <button onclick="hideConfirm()" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let modalAction = null;

        function showConfirm(title, message, onConfirm, isDanger = true) {
            document.getElementById('confirm-title').innerText = title;
            document.getElementById('confirm-message').innerText = message;
            const confirmBtn = document.getElementById('modal-confirm-btn');
            confirmBtn.style.background = isDanger ? 'var(--accent)' : 'var(--primary)';

            modalAction = onConfirm;
            document.getElementById('custom-confirm-modal').style.display = 'flex';
        }

        function hideConfirm() {
            document.getElementById('custom-confirm-modal').style.display = 'none';
            modalAction = null;
        }

        document.getElementById('modal-confirm-btn').addEventListener('click', () => {
            if (modalAction) modalAction();
            hideConfirm();
        });
    </script>

    @auth
        <script>
            // Global variables for Firebase
            let messaging = null;

            // Firebase Configuration (Using config() helper for reliability)
            const firebaseConfig = {
                apiKey: "{{ config('services.firebase.api_key') }}",
                authDomain: "{{ config('services.firebase.auth_domain') }}",
                projectId: "{{ config('services.firebase.project_id') }}",
                storageBucket: "{{ config('services.firebase.storage_bucket') }}",
                messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
                appId: "{{ config('services.firebase.app_id') }}"
            };

            const vapidKey = "{{ config('services.firebase.vapid_key') }}";

            if (firebaseConfig.apiKey && firebaseConfig.apiKey !== 'YOUR_API_KEY') {
                try {
                    firebase.initializeApp(firebaseConfig);
                    messaging = firebase.messaging();

                    messaging.onMessage((payload) => {
                        if (payload.notification) {
                            new Notification(payload.notification.title, {
                                body: payload.notification.body,
                                icon: '/logo.png',
                            });
                        }
                    });
                } catch (e) {
                    console.error('Firebase initialization error:', e);
                }
            }

            async function requestPermission() {
                if (!messaging) {
                    console.warn('FCM Messaging not initialized. Check your Firebase config.');
                    return;
                }

                try {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        // Use the special FCM service worker file for token generation
                        const swPath = "{{ asset('firebase-messaging-sw.js') }}";
                        const registration = await navigator.serviceWorker.register(swPath);
                        await navigator.serviceWorker.ready;

                        const token = await messaging.getToken({
                            vapidKey: vapidKey,
                            serviceWorkerRegistration: registration
                        });

                        if (token) {
                            sendTokenToServer(token);
                        } else {
                            console.warn('No registration token available. Request permission to generate one.');
                        }
                    } else {
                        console.warn('Notification permission denied.');
                    }
                } catch (error) {
                    console.error('An error occurred while retrieving token:', error);
                    alert('Notification Error: ' + error.message);
                }
            }

            function disableNotifications() {
                fetch("{{ route('fcm.token.delete') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Server error');
                        return response.json();
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error disabling notifications:', error);
                    });
            }

            function sendTokenToServer(token) {
                fetch("{{ route('fcm.token') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            token: token
                        })
                    })
                    .then(async (response) => {
                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error(`Server error (${response.status}): ${errorText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error saving token to server:', error);
                        alert('Live Server Error: Failed to save notification token. ' + error.message);
                    });
            }
        </script>
    @endauth
</body>

</html>
