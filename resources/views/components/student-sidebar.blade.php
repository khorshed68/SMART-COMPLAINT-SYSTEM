<aside class="admin-sidebar">
    <div class="sidebar-brand" style="background: linear-gradient(135deg, #071f78 0%, #0d38ba 100%);">
        <i class="fas fa-headset"></i>
        <span>SCS Student</span>
    </div>
    
    <ul class="sidebar-menu" style="height: calc(100% - 140px); overflow-y: auto;">
        <li class="sidebar-menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}"><i class="fas fa-th-large"></i> Dashboard</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('complaints.create') ? 'active' : '' }}">
            <a href="{{ route('complaints.create') }}"><i class="fas fa-plus-circle"></i> New Complaint</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('complaints.index') || Request::routeIs('complaints.show') ? 'active' : '' }}">
            <a href="{{ route('complaints.index') }}"><i class="fas fa-history"></i> My Complaints</a>
        </li>
        <li class="sidebar-menu-item {{ Request::is('announcements*') ? 'active' : '' }}">
            <a href="/announcements"><i class="fas fa-bullhorn"></i> Announcements</a>
        </li>
        <li class="sidebar-menu-item {{ Request::routeIs('profile') ? 'active' : '' }}">
            <a href="{{ route('profile') }}"><i class="fas fa-user-cog"></i> Settings</a>
        </li>
        
        @if(Auth::user()->isAdmin())
            <li class="sidebar-menu-item mt-3">
                <a href="{{ route('admin.dashboard') }}" style="color: #60a5fa;"><i class="fas fa-shield-alt"></i> Admin Panel</a>
            </li>
        @endif
        
        <hr style="border: 0; border-top: 1px solid #1f2937; margin: 15px 0;">
        
        <li class="sidebar-menu-item">
            <form action="{{ route('logout') }}" method="POST" id="student-logout-form" style="display: none;">
                @csrf
            </form>
            <a href="javascript:void(0)" onclick="document.getElementById('student-logout-form').submit();" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
    
    <!-- Student profile footer inside sidebar -->
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 15px; background: #111827; border-top: 1px solid #1f2937; display: flex; align-items: center; gap: 10px;">
        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
        <div style="overflow: hidden; flex-grow: 1;">
            <div style="color: #f3f4f6; font-size: 0.82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
            <div style="color: #9ca3af; font-size: 0.72rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->department ?? 'Student' }}</div>
        </div>
    </div>
</aside>
