<nav class="navbar fade-in">
    <a href="{{ route('dashboard') }}" class="navbar-brand">
        <i class="fas fa-headset"></i>
        <span>{{ setting('site_name', 'Smart Complaint System') }}</span>
    </a>
    
    <ul class="navbar-nav">
        <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i> Dashboard</a>
        </li>
        <li class="{{ Request::routeIs('complaints.create') ? 'active' : '' }}">
            <a href="{{ route('complaints.create') }}"><i class="fas fa-plus-circle mr-1"></i> Submit Complaint</a>
        </li>
        <li class="{{ Request::routeIs('complaints.index') ? 'active' : '' }}">
            <a href="{{ route('complaints.index') }}"><i class="fas fa-list-alt mr-1"></i> My Complaints</a>
        </li>
        <li class="{{ Request::routeIs('profile') ? 'active' : '' }}">
            <a href="{{ route('profile') }}"><i class="fas fa-user-cog mr-1"></i> Settings</a>
        </li>
        @if(Auth::user()->isAdmin())
            <li>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary py-1 px-3 text-white" style="font-size: 0.85rem;"><i class="fas fa-shield-alt"></i> Admin Panel</a>
            </li>
        @endif
    </ul>

    <div class="d-flex align-items-center" style="gap: 20px;">
        <!-- Theme Toggle -->
        <button id="dark-mode-toggle-btn" class="btn btn-outline-primary" style="padding: 8px 12px; border-radius: 50%; border: none; font-size: 1.1rem;">
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
                <span class="font-weight-bold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
            </div>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user mr-2"></i> Profile</a>
                <a class="dropdown-item" href="{{ route('complaints.index') }}"><i class="fas fa-history mr-2"></i> My History</a>
                <hr style="margin: 5px 0; border: 0; border-top: 1px solid #edf2f7;">
                <form action="{{ route('logout') }}" method="POST" onsubmit="submitBtn=$(this).find('button'); submitBtn.prop('disabled', true);">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger w-100 text-left"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // Close dropdown on click outside
    document.addEventListener('click', () => {
        $('.dropdown-menu').removeClass('show');
    });
</script>
