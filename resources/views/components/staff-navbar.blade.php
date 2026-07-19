<header class="admin-topbar">
    <div class="d-flex align-items-center" style="gap: 15px;">
        <!-- Mobile Sidebar Toggle -->
        <button id="sidebar-toggle-btn" class="btn btn-outline-primary d-md-none" style="padding: 6px 12px; background: none; border: 1.5px solid var(--border-color); color: var(--dark);">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Breadcrumbs -->
        <div class="breadcrumbs" style="font-size: 0.85rem; color: var(--dark); font-weight: 500;">
            <span>Staff</span> / 
            <span class="font-weight-bold" style="color: #f59e0b;">
                @if(Request::routeIs('staff.dashboard')) Dashboard
                @elseif(Request::routeIs('profile')) Settings
                @endif
            </span>
        </div>
    </div>

    <div class="topbar-actions">
        <!-- Theme Toggle -->
        <button id="dark-mode-toggle-btn" class="btn btn-outline-primary" style="padding: 8px 12px; border-radius: 50%; border: none; font-size: 1.1rem; cursor: pointer; background: none; color: var(--dark);">
            <i class="fas fa-moon"></i>
        </button>

        <!-- Notification Bell -->
        <div class="bell-container" id="bell-btn" style="cursor: pointer; position: relative;">
            <i class="far fa-bell bell-icon" style="font-size: 1.15rem; color: var(--dark);"></i>
            <span id="bell-count-badge" class="bell-badge d-none" style="position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; border-radius: 50%; font-size: 0.65rem; padding: 2px 6px;">0</span>
            
            <div id="bell-dropdown-list" class="notification-dropdown">
                <div class="notif-header">
                    <span>Notifications</span>
                    <a href="javascript:void(0)" onclick="$.ajax({url: '/api/notifications/read-all', method: 'PUT', success: () => location.reload()})" class="text-primary" style="text-decoration: none; font-size: 0.78rem;">Mark all read</a>
                </div>
                <div id="notif-items-list" class="notif-list">
                    <!-- Loaded via AJAX -->
                </div>
                <div class="notif-footer">
                    <a href="javascript:void(0)" onclick="$.ajax({url: '/api/notifications', method: 'DELETE', success: () => location.reload()})" class="text-danger" style="text-decoration: none; font-size: 0.78rem;">Clear all notifications</a>
                </div>
            </div>
        </div>

        <!-- User profile dropdown -->
        <div class="dropdown">
            <div class="user-dropdown-btn" onclick="$(this).next('.dropdown-menu').toggleClass('show'); event.stopPropagation();" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" class="user-avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                <span class="font-weight-bold d-none d-sm-inline" style="font-size: 0.85rem; color: var(--dark);">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
            </div>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user mr-2"></i> Profile Settings</a>
                <hr style="margin: 5px 0; border: 0; border-top: 1px solid #edf2f7;">
                <form action="{{ route('logout') }}" method="POST" onsubmit="submitBtn=$(this).find('button'); submitBtn.prop('disabled', true);">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger w-100 text-left" style="background: none; border: none; padding: 8px 16px;"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    // Close dropdown on click outside
    document.addEventListener('click', () => {
        $('.dropdown-menu').removeClass('show');
    });
</script>
