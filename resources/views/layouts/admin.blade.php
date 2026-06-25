<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - ' . setting('site_name', 'Smart Complaint System'))</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        @include('components.admin-sidebar')

        <!-- Main Workspace -->
        <div class="admin-main">
            <!-- Top Navigation -->
            @include('components.admin-navbar')

            <!-- View Content -->
            <div class="p-4" style="flex-grow: 1;">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery & Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.0/dist/chart.min.js"></script>
    
    <!-- Scripts -->
    <script src="{{ asset('js/ui-components.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize notification bell
            new NotificationBell('bell-btn', 'bell-count-badge', 'bell-dropdown-list', 'notif-items-list');
            
            // Initialize theme toggle
            new ThemeToggle('dark-mode-toggle-btn');
            
            // Sidebar Mobile Toggle
            $('#sidebar-toggle-btn').click(function() {
                $('.admin-sidebar').toggleClass('active');
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
