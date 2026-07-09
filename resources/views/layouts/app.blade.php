<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name', 'Smart Complaint System'))</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    @auth
        @include('components.navbar')
    @endauth

    <main class="@auth main-content @endauth">
        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts -->
    <script src="{{ asset('js/ui-components.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            @auth
                // Initialize notification bell
                new NotificationBell('bell-btn', 'bell-count-badge', 'bell-dropdown-list', 'notif-items-list');
                
                // Initialize theme toggle
                new ThemeToggle('dark-mode-toggle-btn');
            @endauth

            // Mouse tracking background glow and water wave ripples for auth pages
            const authContainer = document.querySelector('.auth-split-container');
            if (authContainer) {
                let lastX = 0;
                let lastY = 0;

                authContainer.addEventListener('mousemove', e => {
                    const rect = authContainer.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width) * 100;
                    const y = ((e.clientY - rect.top) / rect.height) * 100;
                    authContainer.style.setProperty('--mouse-x', `${x}%`);
                    authContainer.style.setProperty('--mouse-y', `${y}%`);

                    // Check distance threshold to spawn water wave ripple
                    const dist = Math.hypot(e.clientX - lastX, e.clientY - lastY);
                    if (dist > 35) {
                        lastX = e.clientX;
                        lastY = e.clientY;

                        const ripple = document.createElement('div');
                        ripple.className = 'bg-ripple';
                        
                        const posX = e.clientX - rect.left;
                        const posY = e.clientY - rect.top;
                        ripple.style.left = `${posX}px`;
                        ripple.style.top = `${posY}px`;

                        authContainer.appendChild(ripple);

                        // Remove element after transition completes
                        setTimeout(() => {
                            ripple.remove();
                        }, 800);
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
