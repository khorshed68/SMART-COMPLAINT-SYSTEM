@extends('layouts.app')

@section('title', 'Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="container fade-in">
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
    });
</script>
@endsection
