@extends('layouts.app')

@section('title', 'Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="container fade-in">
    <!-- Campus Announcements Outage Banner -->
    <div id="dashboard-announcements-container" class="mb-4 d-none">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--warning) !important; background-color: rgba(243, 156, 18, 0.08); border-radius: 8px; overflow: hidden;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; font-size: 1.1rem;">
                    <i class="fas fa-bullhorn animate-pulse"></i>
                </div>
                <div style="flex-grow: 1; overflow: hidden;">
                    <div class="font-weight-bold" style="color: var(--dark); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                        <span id="ann-badge-type" class="badge" style="font-size: 0.72rem; text-transform: uppercase;">Maintenance</span>
                        <span id="ann-ticker-title">Loading active announcements...</span>
                    </div>
                    <p id="ann-ticker-desc" class="text-muted mb-0 mt-1" style="font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        Please stand by...
                    </p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span id="ann-ticker-timer" class="badge bg-dark font-weight-bold" style="font-size: 0.78rem;"></span>
                    <a href="/announcements" class="btn btn-sm btn-light font-weight-bold py-1 px-2.5" style="font-size: 0.72rem; border-radius: 4px; text-decoration: none;">View Board</a>
                    <button class="btn btn-sm btn-outline-secondary border-0" id="btn-next-ann" style="padding: 4px 8px; display: none;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner" style="position: relative; background: linear-gradient(135deg, #071f78 0%, #0d38ba 60%, #581c87 100%); overflow: hidden; padding: 50px 40px; border-radius: 18px; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 15px 35px rgba(13, 56, 186, 0.25);">
        <!-- Wave & Glow Background Overlays (CSS vector elements) -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
            <svg width="100%" height="100%" viewBox="0 0 1000 240" preserveAspectRatio="none" fill="none">
                <!-- Cyan glowing bubble left bottom -->
                <circle cx="480" cy="220" r="140" fill="#2563eb" opacity="0.3" filter="blur(65px)" />
                <!-- Purple glowing bubble top right -->
                <circle cx="850" cy="40" r="160" fill="#8b5cf6" opacity="0.25" filter="blur(75px)" />
                <!-- Curved waves -->
                <path d="M 0 240 Q 300 130, 600 215 T 1000 145 L 1000 240 Z" fill="#0d38ba" opacity="0.3" />
                <path d="M 0 240 Q 400 100, 750 225 T 1000 105 L 1000 240 Z" fill="#2563eb" opacity="0.15" />
                <!-- Small decorative elements -->
                <circle cx="680" cy="60" r="4.5" fill="#a855f7" opacity="0.5" />
                <circle cx="910" cy="100" r="5" fill="#60a5fa" opacity="0.4" />
                <circle cx="450" cy="120" r="8" fill="#3b82f6" opacity="0.15" />
                <circle cx="900" cy="140" r="3.5" fill="#ffffff" opacity="0.75" />
            </svg>
        </div>

        <div class="welcome-banner-content" style="position: relative; z-index: 2; max-width: 100%;">
            <h1 class="welcome-title" style="font-size: 2.25rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">Hello, {{ Auth::user()->name }}! 👋</h1>
            <p class="welcome-subtitle" style="font-size: 1.05rem; opacity: 0.9; line-height: 1.65; margin-bottom: 25px; color: #ffffff; max-width: 90%;">Welcome to your workspace dashboard. Submit a campus complaint or track your issue resolutions in real-time.</p>
            <div class="welcome-meta" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 25px;">
                <span class="welcome-date-badge" style="border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.05); color: #cbd5e1; font-size: 0.8rem; border-radius: 30px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="far fa-calendar-alt"></i> {{ now()->format('l, F j, Y') }}
                </span>
                <span class="welcome-date-badge" style="border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.05); color: #cbd5e1; font-size: 0.8rem; border-radius: 30px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-shield-alt"></i> Empowering student voices
                </span>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-3 mt-3">
                <a href="{{ route('complaints.create') }}" class="btn py-2.5 px-4 font-weight-bold" style="background-color: #2563eb; border: none; border-radius: 20px; color: #fff; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-plus"></i> File a Complaint</a>
                <a href="/announcements" class="btn btn-outline-light py-2.5 px-4 font-weight-bold" style="border-radius: 20px; border: 1.5px solid #8b5cf6; background: transparent; color: #ffffff; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-bullhorn"></i> Bulletins Board</a>
            </div>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="row">
        <!-- Total Filed -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-total">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-blue">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div>
                        <div id="stat-total" class="stat-val">0</div>
                        <div class="stat-lbl">Total Filed</div>
                        <div class="stat-desc">All complaints you've submitted</div>
                    </div>
                </div>
                <!-- Mini Trendline (SVG) -->
                <div class="stat-chart">
                    <svg viewBox="0 0 100 40" class="trendline">
                        <path d="M 0 30 Q 25 10, 50 25 T 100 15" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" />
                        <circle cx="100" cy="15" r="3" fill="#3b82f6" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Pending -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-pending">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div id="stat-pending" class="stat-val">0</div>
                        <div class="stat-lbl">Pending</div>
                        <div class="stat-desc">Awaiting review or action</div>
                    </div>
                </div>
                <!-- Mini Trendline (SVG) -->
                <div class="stat-chart">
                    <svg viewBox="0 0 100 40" class="trendline">
                        <path d="M 0 25 Q 30 15, 60 30 T 100 10" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" />
                        <circle cx="100" cy="10" r="3" fill="#f59e0b" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-progress">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-purple">
                        <i class="fas fa-sync-alt fa-spin"></i>
                    </div>
                    <div>
                        <div id="stat-in_progress" class="stat-val">0</div>
                        <div class="stat-lbl">In Progress</div>
                        <div class="stat-desc">Currently being processed</div>
                    </div>
                </div>
                <!-- Mini Trendline (SVG) -->
                <div class="stat-chart">
                    <svg viewBox="0 0 100 40" class="trendline">
                        <path d="M 0 35 Q 25 5, 50 30 T 100 20" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" />
                        <circle cx="100" cy="20" r="3" fill="#8b5cf6" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-resolved">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div id="stat-resolved" class="stat-val">0</div>
                        <div class="stat-lbl">Resolved</div>
                        <div class="stat-desc">Successfully resolved issues</div>
                    </div>
                </div>
                <!-- Mini Trendline (SVG) -->
                <div class="stat-chart">
                    <svg viewBox="0 0 100 40" class="trendline">
                        <path d="M 0 30 Q 30 40, 60 10 T 100 15" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" />
                        <circle cx="100" cy="15" r="3" fill="#10b981" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="dashboard-section-header mt-2 mb-3">
        <h2 class="dashboard-section-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b;"><i class="fas fa-rocket text-primary mr-1"></i> Quick Actions</h2>
    </div>
    <div class="quick-actions-row">
        <!-- Submit Complaint -->
        <a href="{{ route('complaints.create') }}" class="quick-action-card-horizontal action-submit">
            <div class="action-card-left">
                <div class="action-icon-circle bg-blue">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div class="action-title">Submit Complaint</div>
                    <div class="action-desc">File a new issue or campus complaint</div>
                </div>
            </div>
            <div class="action-arrow-circle">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <!-- Complaint History -->
        <a href="{{ route('complaints.index') }}" class="quick-action-card-horizontal action-history">
            <div class="action-card-left">
                <div class="action-icon-circle bg-purple">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <div class="action-title">Complaint History</div>
                    <div class="action-desc">Track status and review past feedback</div>
                </div>
            </div>
            <div class="action-arrow-circle">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <!-- Profile Settings -->
        <a href="{{ route('profile') }}" class="quick-action-card-horizontal action-settings">
            <div class="action-card-left">
                <div class="action-icon-circle bg-green">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div>
                    <div class="action-title">Profile Settings</div>
                    <div class="action-desc">Update contact or credentials info</div>
                </div>
            </div>
            <div class="action-arrow-circle">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Recent complaints table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-table-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span style="font-weight: 700; color: #1e293b;"><i class="fas fa-list-alt text-primary mr-1"></i> Recent Complaints</span>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-primary btn-sm py-1 px-3" style="font-size: 0.8rem; border-radius: 8px;">View All &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComplaints as $complaint)
                                    <tr>
                                        <td><span class="font-weight-bold">#{{ $complaint->id }}</span></td>
                                        <td>{{ $complaint->title }}</td>
                                        <td>{{ $complaint->category->name ?? 'Other' }}</td>
                                        <td><x-priority-badge :priority="$complaint->priority" /></td>
                                        <td><x-status-badge :status="$complaint->status" /></td>
                                        <td>{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-outline-primary btn-sm py-1 px-3" style="font-size: 0.75rem; border-radius: 6px;">Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <!-- Empty State Vector (SVG) -->
                                                <svg viewBox="0 0 120 120" width="80" height="80" style="margin-bottom: 16px;">
                                                    <defs>
                                                        <linearGradient id="grad-folder" x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0%" stop-color="#93c5fd"/>
                                                            <stop offset="100%" stop-color="#3b82f6"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <path d="M 20 40 L 45 40 L 53 48 L 100 48 L 100 90 L 20 90 Z" fill="url(#grad-folder)" opacity="0.3" />
                                                    <rect x="35" y="25" width="50" height="60" rx="4" fill="#ffffff" stroke="#93c5fd" stroke-width="1.5" transform="rotate(-5, 60, 55)" />
                                                    <line x1="45" y1="40" x2="75" y2="40" stroke="#cbd5e1" stroke-width="2" />
                                                    <line x1="45" y1="50" x2="70" y2="50" stroke="#cbd5e1" stroke-width="2" />
                                                    <line x1="45" y1="60" x2="60" y2="60" stroke="#cbd5e1" stroke-width="2" />
                                                    
                                                    <path d="M 20 50 L 100 50 L 100 90 L 20 90 Z" fill="url(#grad-folder)" />
                                                    <circle cx="85" cy="75" r="10" fill="#3b82f6" />
                                                    <path d="M 81 75 L 84 78 L 89 73" stroke="#ffffff" stroke-width="2" fill="none" stroke-linecap="round" />
                                                </svg>
                                                <h5 class="font-weight-bold mb-1" style="color: #1e293b;">No complaints yet!</h5>
                                                <p class="text-muted mb-0" style="font-size: 0.82rem;">You haven't submitted any complaints yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Load initial counters
        loadDashboardStats();

        // 30s Auto refresh counters
        setInterval(function() {
            loadDashboardStats();
        }, 30000);

        // Load active announcements
        let activeAnnouncements = [];
        let currentAnnIndex = 0;

        function loadActiveAnnouncements() {
            $.get('/api/announcements/active', function(data) {
                if (data.length === 0) {
                    $('#dashboard-announcements-container').addClass('d-none');
                    return;
                }

                activeAnnouncements = data;
                currentAnnIndex = 0;
                displayAnnouncement(currentAnnIndex);
                $('#dashboard-announcements-container').removeClass('d-none');

                if (data.length > 1) {
                    $('#btn-next-ann').show();
                } else {
                    $('#btn-next-ann').hide();
                }
            });
        }

        function displayAnnouncement(index) {
            if (activeAnnouncements.length === 0) return;
            const ann = activeAnnouncements[index];
            
            // Set details
            $('#ann-ticker-title').text(ann.title);
            $('#ann-ticker-desc').text(ann.content);

            if (ann.category) {
                $('#ann-badge-type').text(ann.category.name).css('background-color', ann.category.color);
            } else {
                $('#ann-badge-type').text('Announcement').css('background-color', 'var(--warning)');
            }

            // Estimate time remaining
            const endTime = new Date(ann.end_time);
            const now = new Date();
            const diffMs = endTime - now;
            
            if (diffMs > 0) {
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                
                if (diffHours > 0) {
                    $('#ann-ticker-timer').text(`Ends in ~${diffHours}h`).show();
                } else {
                    $('#ann-ticker-timer').text(`Ends in ~${diffMins}m`).show();
                }
            } else {
                $('#ann-ticker-timer').hide();
            }
        }

        $('#btn-next-ann').click(function() {
            currentAnnIndex = (currentAnnIndex + 1) % activeAnnouncements.length;
            displayAnnouncement(currentAnnIndex);
        });

        // Load active announcements immediately
        loadActiveAnnouncements();
    });
</script>
@endsection
