<header class="admin-topbar">
    <div class="d-flex align-items-center" style="gap: 15px;">
        <!-- Mobile Sidebar Toggle -->
        <button id="sidebar-toggle-btn" class="btn btn-outline-primary d-md-none" style="padding: 6px 12px;">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <span>Admin</span> / 
            <span class="font-weight-bold">
                @if(Request::routeIs('admin.dashboard')) Dashboard
                @elseif(Request::routeIs('admin.complaints')) Complaints Management
                @elseif(Request::routeIs('admin.complaints.show')) Complaint Details
                @elseif(Request::routeIs('admin.users')) Users Management
                @elseif(Request::routeIs('admin.categories')) Categories Management
                @elseif(Request::routeIs('admin.analytics')) Analytics Dashboard
                @elseif(Request::routeIs('admin.settings')) System Settings
                @elseif(Request::routeIs('admin.control-panel')) Control Panel
                @elseif(Request::routeIs('admin.system-health')) System Health Monitor
                @elseif(Request::routeIs('admin.change-password')) Reset Password
                @endif
            </span>
        </div>
    </div>

    <div class="topbar-actions">
        <!-- Theme Toggle -->
        <button id="dark-mode-toggle-btn" class="btn btn-outline-primary" style="padding: 8px 12px; border-radius: 50%; border: none; font-size: 1.1rem; cursor: pointer;">
            <i class="fas fa-moon"></i>
        </button>

        <!-- Notification Bell -->
        <div class="bell-container" id="bell-btn">
            <i class="far fa-bell bell-icon"></i>
            <span id="bell-count-badge" class="bell-badge d-none">0</span>
            
            <div id="bell-dropdown-list" class="notification-dropdown">
                <div class="notif-header">
                    <span>Notifications</span>
                    <a href="javascript:void(0)" onclick="$.ajax({url: '/api/notifications/read-all', method: 'PUT', success: () => location.reload()})" class="text-primary" style="text-decoration: none;">Mark all read</a>
                </div>
                <div id="notif-items-list" class="notif-list">
                    <!-- Loaded via AJAX -->
                </div>
                <div class="notif-footer">
                    <a href="javascript:void(0)" onclick="$.ajax({url: '/api/notifications', method: 'DELETE', success: () => location.reload()})" class="text-danger" style="text-decoration: none;">Clear all notifications</a>
                </div>
            </div>
        </div>

        <!-- User profile dropdown -->
        <div class="dropdown">
            <div class="user-dropdown-btn" onclick="$(this).next('.dropdown-menu').toggleClass('show'); event.stopPropagation();">
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" class="user-avatar">
                <span class="font-weight-bold" style="font-size: 0.9rem; color: var(--dark);">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
            </div>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user mr-2"></i> Profile Settings</a>
                <a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-external-link-alt mr-2"></i> Client Area</a>
                <hr style="margin: 5px 0; border: 0; border-top: 1px solid #edf2f7;">
                <form action="{{ route('logout') }}" method="POST" onsubmit="submitBtn=$(this).find('button'); submitBtn.prop('disabled', true);">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger w-100 text-left"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
