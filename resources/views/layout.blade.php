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
            padding: 20px;
        }

        header {
            padding: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
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
</head>

<body class="{{ isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'theme-light' : '' }}">
    <div class="container">
        <header>
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit;">
                <div class="logo">Roomie</div>
            </a>
            @auth
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="text-align: right;">
                        <a href="{{ route('profile.edit') }}"
                            style="text-decoration: none; color: inherit; display: block;">
                            <p style="font-size: 0.75rem; font-weight: 700;">{{ Auth::user()->name }}</p>
                        </a>
                        <a href="{{ route('logout') }}"
                            style="font-size: 0.625rem; color: var(--accent); text-decoration: none; display: block; margin-top: 2px;">Logout</a>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="user-pill" style="text-decoration: none;">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}"
                                style="width: 32px; height: 32px; border-radius: 16px; object-fit: cover; border: 1px solid var(--primary-light);">
                        @else
                            <i class="fa-solid fa-circle-user" style="font-size: 2rem; color: var(--text-dim);"></i>
                        @endif
                    </a>
                </div>
            @endauth
        </header>

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
</body>

</html>
