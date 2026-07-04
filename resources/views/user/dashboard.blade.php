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
        <div class="welcome-banner-content">
            <h1 class="welcome-title">Hello, {{ Auth::user()->name }}!</h1>
            <p class="welcome-subtitle">Welcome to your workspace dashboard. Submit a campus complaint or track your issue resolutions in real-time.</p>
            <div class="welcome-meta">
                <span class="welcome-date-badge">
                    <i class="far fa-calendar-alt"></i> {{ now()->format('l, F j, Y') }}
                </span>
                <span class="welcome-date-badge" style="background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.25);">
                    <i class="fas fa-shield-alt"></i> Empowering student voices
                </span>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-3 mt-3">
                <a href="{{ route('complaints.create') }}" class="btn py-2.5 px-4 font-weight-bold" style="background-color: #10b981; border: none; border-radius: 30px; color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.2s ease;"><i class="fas fa-plus mr-1"></i> File a Complaint</a>
                <a href="/announcements" class="btn btn-outline-light py-2.5 px-4 font-weight-bold" style="border-radius: 30px; border: 1px solid rgba(255, 255, 255, 0.3);"><i class="fas fa-bullhorn mr-1"></i> Bulletins board</a>
            </div>
        </div>

        <!-- System Resolution Widget -->
        <div class="d-none d-lg-block welcome-hero-widget">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="font-weight-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #a5b4fc !important; text-transform: uppercase;">Resolution rate</span>
                <span class="badge bg-success" style="font-size: 0.65rem; font-weight: 700; background-color: rgba(16, 185, 129, 0.2) !important; color: #10b981 !important;">+5.8%</span>
            </div>
            <div class="d-flex align-items-baseline mb-2">
                <span class="font-weight-bold" style="font-size: 1.8rem; color: #fff;">96.8%</span>
                <span class="text-muted ml-1" style="font-size: 0.75rem; color: #94a3b8 !important;">avg response</span>
            </div>
            <div class="progress" style="height: 6px; background-color: rgba(255, 255, 255, 0.1); border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                <div class="progress-bar" role="progressbar" style="width: 96.8%; background: linear-gradient(to right, #818cf8, #c084fc);" aria-valuenow="96.8" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem; color: #94a3b8 !important;">
                <span>Target: 95%</span>
                <span>Active Month</span>
            </div>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-total">
                <div>
                    <div id="stat-total" class="stat-val">0</div>
                    <div class="stat-lbl">Total Filed</div>
                </div>
                <div class="stat-icon-bubble" style="background-color: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-pending">
                <div>
                    <div id="stat-pending" class="stat-val">0</div>
                    <div class="stat-lbl">Pending</div>
                </div>
                <div class="stat-icon-bubble" style="background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-progress">
                <div>
                    <div id="stat-in_progress" class="stat-val">0</div>
                    <div class="stat-lbl">In Progress</div>
                </div>
                <div class="stat-icon-bubble" style="background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-resolved">
                <div>
                    <div id="stat-resolved" class="stat-val">0</div>
                    <div class="stat-lbl">Resolved</div>
                </div>
                <div class="stat-icon-bubble" style="background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="dashboard-section-header mt-4">
        <h2 class="dashboard-section-title"><i class="fas fa-rocket"></i> Quick Actions</h2>
    </div>
    <div class="quick-actions-grid">
        <a href="{{ route('complaints.create') }}" class="quick-action-btn action-submit">
            <div class="quick-action-icon"><i class="fas fa-plus"></i></div>
            <div class="quick-action-title">Submit Complaint</div>
            <div class="quick-action-desc">File a new issue or campus complaint</div>
        </a>
        <a href="{{ route('complaints.index') }}" class="quick-action-btn action-history">
            <div class="quick-action-icon"><i class="fas fa-history"></i></div>
            <div class="quick-action-title">Complaint History</div>
            <div class="quick-action-desc">Track status and review past feedback</div>
        </a>
        <a href="{{ route('profile') }}" class="quick-action-btn action-settings">
            <div class="quick-action-icon"><i class="fas fa-user-cog"></i></div>
            <div class="quick-action-title">Profile Settings</div>
            <div class="quick-action-desc">Update contact or credentials info</div>
        </a>
    </div>

    <!-- Recent complaints table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-table-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
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
