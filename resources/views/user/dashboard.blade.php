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
    <div class="welcome-banner">
        <h1 class="welcome-title">Hello, {{ Auth::user()->name }}!</h1>
        <p class="welcome-subtitle">Welcome to your dashboard. Submit a complaint or track your history in real-time.</p>
    </div>

    <!-- Stats grid -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div>
                    <div id="stat-total" class="stat-val">0</div>
                    <div class="stat-lbl">Total Filed</div>
                </div>
                <div class="stat-icon"><i class="fas fa-folder"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-left-color: var(--warning);">
                <div>
                    <div id="stat-pending" class="stat-val">0</div>
                    <div class="stat-lbl">Pending</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock text-warning"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-left-color: var(--primary);">
                <div>
                    <div id="stat-in_progress" class="stat-val">0</div>
                    <div class="stat-lbl">In Progress</div>
                </div>
                <div class="stat-icon"><i class="fas fa-spinner fa-spin text-primary"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="border-left-color: var(--secondary);">
                <div>
                    <div id="stat-resolved" class="stat-val">0</div>
                    <div class="stat-lbl">Resolved</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle text-secondary"></i></div>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="dashboard-section-header mt-4">
        <h2 class="dashboard-section-title"><i class="fas fa-rocket"></i> Quick Actions</h2>
    </div>
    <div class="quick-actions-grid">
        <a href="{{ route('complaints.create') }}" class="quick-action-btn submit-action">
            <div class="quick-action-icon"><i class="fas fa-plus"></i></div>
            <div class="quick-action-title">Submit Complaint</div>
            <div class="quick-action-desc">File a new issue/complaint</div>
        </a>
        <a href="{{ route('complaints.index') }}" class="quick-action-btn">
            <div class="quick-action-icon"><i class="fas fa-history"></i></div>
            <div class="quick-action-title">Complaint History</div>
            <div class="quick-action-desc">Track status of past complaints</div>
        </a>
        <a href="{{ route('profile') }}" class="quick-action-btn">
            <div class="quick-action-icon"><i class="fas fa-user-cog"></i></div>
            <div class="quick-action-title">Profile Settings</div>
            <div class="quick-action-desc">Update contact or login info</div>
        </a>
    </div>

    <!-- Recent complaints table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Complaints</span>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-primary btn-sm py-1 px-3" style="font-size: 0.8rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
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
                                            <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-outline-primary btn-sm py-1 px-3" style="font-size: 0.75rem;">Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted p-4">You haven't submitted any complaints yet.</td>
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
