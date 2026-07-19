@extends('layouts.app')

@section('title', 'Student Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.0/dist/chart.min.js"></script>
<style>
    /* Premium visual enhancements & layout spacing */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
    }
    
    .timeline-item {
        position: relative;
        padding-left: 24px;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--primary);
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 8px;
        top: 18px;
        bottom: -22px;
        width: 2px;
        background-color: var(--border-color, #e2e8f0);
    }
    .timeline-item:last-child::after {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid fade-in" style="padding: 0 15px;">
    <!-- Active Announcements Outage Ticker -->
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

    <!-- Welcome Hero Card -->
    <div class="welcome-banner" style="position: relative; background: linear-gradient(135deg, #2563EB 0%, #4F46E5 60%, #7C3AED 100%); overflow: hidden; padding: 40px; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 15px 35px rgba(79, 70, 229, 0.25); color: white; display: flex; align-items: center; justify-content: space-between; gap: 30px; margin-bottom: 30px;">
        <!-- Wave and Glow vector elements -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
            <svg width="100%" height="100%" viewBox="0 0 1000 240" preserveAspectRatio="none" fill="none">
                <circle cx="480" cy="220" r="140" fill="#2563eb" opacity="0.3" filter="blur(65px)" />
                <circle cx="850" cy="40" r="160" fill="#8b5cf6" opacity="0.25" filter="blur(75px)" />
                <path d="M 0 240 Q 300 130, 600 215 T 1000 145 L 1000 240 Z" fill="#0d38ba" opacity="0.3" />
                <path d="M 0 240 Q 400 100, 750 225 T 1000 105 L 1000 240 Z" fill="#2563eb" opacity="0.15" />
            </svg>
        </div>

        <div style="flex-grow: 1; z-index: 2; position: relative;">
            <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 8px;">Hello, {{ Auth::user()->name }}! 👋</h1>
            <p style="font-size: 1.05rem; opacity: 0.9; margin-bottom: 20px;">Welcome back! You've submitted <strong>{{ $complaintsThisMonth }}</strong> complaints this month.</p>
            
            <div class="d-flex flex-wrap gap-2" style="margin-bottom: 25px;">
                <span style="border: 1px solid rgba(255, 255, 255, 0.25); background: rgba(255, 255, 255, 0.08); color: #e2e8f0; font-size: 0.8rem; border-radius: 30px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="far fa-calendar-alt"></i> {{ now()->format('l, F j, Y') }}
                </span>
                <span style="border: 1px solid rgba(255, 255, 255, 0.25); background: rgba(255, 255, 255, 0.08); color: #e2e8f0; font-size: 0.8rem; border-radius: 30px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-percent"></i> Resolved Rate: {{ $stats->total > 0 ? round(($stats->resolved / $stats->total) * 100) : 0 }}%
                </span>
            </div>
            
            <!-- Monthly Progress bar -->
            <div style="max-width: 400px; margin-bottom: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.85rem; opacity: 0.95;">
                    <span>Monthly Resolution Progress</span>
                    <strong>{{ $stats->total > 0 ? round(($stats->resolved / $stats->total) * 100) : 0 }}%</strong>
                </div>
                <div class="progress" style="height: 8px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $stats->total > 0 ? ($stats->resolved / $stats->total) * 100 : 0 }}%; background: #10B981; border-radius: 10px;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-3 mt-3">
                <a href="{{ route('complaints.create') }}" class="btn py-2.5 px-4 font-weight-bold" style="background-color: #ffffff; border: none; border-radius: 20px; color: #2563EB; box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-plus"></i> New Complaint</a>
                <a href="{{ route('complaints.index') }}" class="btn py-2.5 px-4 font-weight-bold" style="border-radius: 20px; border: 1.5px solid rgba(255, 255, 255, 0.5); background: transparent; color: #ffffff; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-history"></i> View History</a>
            </div>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="row">
        <!-- Total Filed -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-total" style="border-left: 4px solid #2563EB; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-blue">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div>
                        <div id="stat-total" class="stat-val" style="color: #2563EB;">0</div>
                        <div class="stat-lbl">Total Filed</div>
                        <div class="stat-desc">+2 this week</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-pending" style="border-left: 4px solid #F59E0B; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div id="stat-pending" class="stat-val" style="color: #F59E0B;">0</div>
                        <div class="stat-lbl">Pending</div>
                        <div class="stat-desc">Awaiting review</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-progress" style="border-left: 4px solid #7C3AED; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-purple">
                        <i class="fas fa-sync-alt fa-spin"></i>
                    </div>
                    <div>
                        <div id="stat-in_progress" class="stat-val" style="color: #7C3AED;">0</div>
                        <div class="stat-lbl">Processing</div>
                        <div class="stat-desc">Active investigation</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card stat-resolved" style="border-left: 4px solid #10B981; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div id="stat-resolved" class="stat-val" style="color: #10B981;">0</div>
                        <div class="stat-lbl">Resolved</div>
                        <div class="stat-desc">Resolution complete</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Details Grid -->
    <div class="row">
        <!-- Left: Charts -->
        <div class="col-lg-7 mb-4">
            <!-- Line Chart: Trends -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 30px !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 1rem;"><i class="fas fa-chart-line text-primary mr-1"></i> Complaint Trends</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="student-trends-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Categories pie distribution -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 30px !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 1rem;"><i class="fas fa-chart-pie text-primary mr-1"></i> Complaint Categories</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 250px; position: relative;">
                        <canvas id="student-categories-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Activity & Profile -->
        <div class="col-lg-4 offset-lg-1 mb-4">
            <!-- Student Profile Summary Card -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; background: linear-gradient(to bottom, #f8fafc, #ffffff); margin-bottom: 30px !important;">
                <div class="card-body p-4 text-center">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 12px;">
                    <h5 class="font-weight-bold mb-1" style="color: #1e293b; font-size: 1.05rem;">{{ Auth::user()->name }}</h5>
                    <p class="text-primary mb-3" style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase;">{{ Auth::user()->department ?? 'Computer Science' }}</p>
                    
                    <div style="text-align: left; font-size: 0.8rem; color: #4a5568; background: #f1f5f9; border-radius: 10px; padding: 15px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phone:</span>
                            <span class="font-weight-bold">{{ Auth::user()->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Member Since:</span>
                            <span class="font-weight-bold">{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Filed:</span>
                            <span class="font-weight-bold">{{ $stats->total }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Resolved:</span>
                            <span class="font-weight-bold" style="color: #10B981;">{{ $stats->resolved }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Timeline -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 30px !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 1rem;"><i class="fas fa-stream text-primary mr-1"></i> Recent Activity</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($recentActivity as $activity)
                        <div class="timeline-item">
                            <div style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                                @if($activity->update_type === 'status_change')
                                    @if($activity->new_status === 'Resolved')
                                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Resolved</span>
                                    @elseif($activity->new_status === 'Rejected')
                                        <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-clock mr-1"></i> Status: {{ $activity->new_status }}</span>
                                    @endif
                                @elseif($activity->update_type === 'assignment')
                                    <span class="text-info"><i class="fas fa-user-tag mr-1"></i> Assigned</span>
                                @else
                                    <span class="text-primary"><i class="fas fa-comment-alt mr-1"></i> Comment</span>
                                @endif
                                <small class="text-muted float-right" style="font-size: 0.72rem;">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.78rem;">
                                Complaint: <span class="font-weight-bold">"{{ $activity->complaint_title }}"</span>
                                @if($activity->comment)
                                    <p class="mb-0 mt-1" style="font-style: italic; background: #f8fafc; padding: 6px 10px; border-radius: 6px;">"{{ Str::limit($activity->comment, 60) }}"</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0" style="font-size: 0.82rem;">No recent activity logged.</p>
                    @endforelse
                </div>
            </div>

            <!-- Resolution rates -->
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 30px !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 1rem;"><i class="fas fa-tachometer-alt text-primary mr-1"></i> Resolution Stats</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="font-size: 0.82rem; color: #4a5568;">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Average Resolution Time</span>
                                <span class="font-weight-bold text-dark">{{ $avgResolutionDays ? $avgResolutionDays . ' Days' : 'N/A' }}</span>
                            </div>
                            <div class="progress" style="height: 6px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <div class="progress-bar bg-info" style="width: {{ $avgResolutionDays ? min(($avgResolutionDays / 10) * 100, 100) : 0 }}%;"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Overall Resolution Rate</span>
                                <span class="font-weight-bold text-dark">{{ $stats->total > 0 ? round(($stats->resolved / $stats->total) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <div class="progress-bar bg-success" style="width: {{ $stats->total > 0 ? ($stats->resolved / $stats->total) * 100 : 0 }}%;"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span>Active Complaints</span>
                            <span class="badge bg-primary px-3 py-1.5" style="border-radius: 10px;">{{ $stats->pending + $stats->in_progress }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white pt-4 px-4 border-0 d-flex justify-content-between align-items-center">
                    <span style="font-weight: 700; color: #1e293b; font-size: 1rem;"><i class="fas fa-list-alt text-primary mr-1"></i> Recent Complaints</span>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-primary btn-sm py-1.5 px-3" style="font-size: 0.8rem; border-radius: 8px;">View All &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 15px 20px;">ID</th>
                                    <th style="padding: 15px 20px;">Title</th>
                                    <th style="padding: 15px 20px;">Category</th>
                                    <th style="padding: 15px 20px;">Priority</th>
                                    <th style="padding: 15px 20px;">Status</th>
                                    <th style="padding: 15px 20px;">Submitted Date</th>
                                    <th style="padding: 15px 20px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentComplaints as $complaint)
                                    <tr>
                                        <td style="padding: 15px 20px;"><span class="font-weight-bold">#{{ $complaint->id }}</span></td>
                                        <td style="padding: 15px 20px;">{{ $complaint->title }}</td>
                                        <td style="padding: 15px 20px;">{{ $complaint->category->name ?? 'Other' }}</td>
                                        <td style="padding: 15px 20px;"><x-priority-badge :priority="$complaint->priority" /></td>
                                        <td style="padding: 15px 20px;"><x-status-badge :status="$complaint->status" /></td>
                                        <td style="padding: 15px 20px;">{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                                        <td style="padding: 15px 20px;">
                                            <a href="{{ route('complaints.show', $complaint->id) }}" class="btn btn-outline-primary btn-sm py-1 px-3" style="font-size: 0.75rem; border-radius: 6px;">Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div style="font-size: 3rem; margin-bottom: 12px;">📂</div>
                                                <h5 class="font-weight-bold mb-1" style="color: #1e293b;">No complaints yet!</h5>
                                                <p class="text-muted mb-3" style="font-size: 0.82rem;">Everything looks good! Submit your first report below.</p>
                                                <a href="{{ route('complaints.create') }}" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 20px; font-size: 0.8rem;">[ File Your First Complaint ]</a>
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

        // Render Charts using passed analytics parameters
        // 1. Complaint Trends Chart
        const trendCtx = document.getElementById('student-trends-chart').getContext('2d');
        const months = {!! json_encode($monthlyStats->pluck('month')) !!};
        const counts = {!! json_encode($monthlyStats->pluck('count')) !!};

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: months.length ? months : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Complaints',
                    data: counts.length ? counts : [0, 0, 0, 0, 0, 0],
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.03)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Complaint Categories Chart
        const catCtx = document.getElementById('student-categories-chart').getContext('2d');
        const catNames = {!! json_encode($categoryStats->pluck('name')) !!};
        const catCounts = {!! json_encode($categoryStats->pluck('count')) !!};

        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catNames.length ? catNames : ['No Data'],
                datasets: [{
                    data: catCounts.length ? catCounts : [1],
                    backgroundColor: [
                        '#2563EB', '#4F46E5', '#7C3AED', '#10B981', '#F59E0B', '#EF4444'
                    ],
                    borderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                },
                cutout: '65%'
            }
        });

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
