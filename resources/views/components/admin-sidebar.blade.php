<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-shield-alt"></i>
        <span>Admin Panel</span>
    </div>
    
    <ul class="sidebar-menu">
        <li class="sidebar-menu-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-th-large"></i> Dashboard</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.complaints') || Request::routeIs('admin.complaints.show') ? 'active' : '' }}">
            <a href="{{ route('admin.complaints') }}"><i class="fas fa-ticket-alt"></i> Complaints</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.users') ? 'active' : '' }}">
            <a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.categories') ? 'active' : '' }}">
            <a href="{{ route('admin.categories') }}"><i class="fas fa-folder-open"></i> Categories</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.analytics') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics') }}"><i class="fas fa-chart-pie"></i> Analytics</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.settings') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}"><i class="fas fa-sliders-h"></i> Settings</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.control-panel') ? 'active' : '' }}">
            <a href="{{ route('admin.control-panel') }}"><i class="fas fa-cogs"></i> Control Panel</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.system-health') ? 'active' : '' }}">
            <a href="{{ route('admin.system-health') }}"><i class="fas fa-heartbeat"></i> System Health</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('admin.change-password') ? 'active' : '' }}">
            <a href="{{ route('admin.change-password') }}"><i class="fas fa-key"></i> Change Password</a>
        </li>
        
        <hr style="border: 0; border-top: 1px solid #1f2937; margin: 15px 0;">
        
        <li class="sidebar-menu-item">
            <a href="{{ route('dashboard') }}"><i class="fas fa-external-link-alt"></i> Back to Site</a>
        </li>
        <li class="sidebar-menu-item">
            <form action="{{ route('logout') }}" method="POST" id="admin-logout-form" style="display: none;">
                @csrf
            </form>
            <a href="javascript:void(0)" onclick="document.getElementById('admin-logout-form').submit();"><i class="fas fa-sign-out-alt text-danger"></i> Logout</a>
        </li>
    </ul>
</aside>
