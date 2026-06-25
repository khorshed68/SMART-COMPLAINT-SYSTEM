@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div id="admin-stat-total" class="stat-val">0</div>
                <div class="stat-lbl">Total Complaints</div>
            </div>
            <div class="stat-icon"><i class="fas fa-folder"></i></div>
        </div>
        <div class="stat-card pending">
            <div>
                <div id="admin-stat-pending" class="stat-val">0</div>
                <div class="stat-lbl">Pending Review</div>
            </div>
            <div class="stat-icon"><i class="fas fa-clock text-warning"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div id="admin-stat-in-progress" class="stat-val">0</div>
                <div class="stat-lbl">In Progress</div>
            </div>
            <div class="stat-icon"><i class="fas fa-spinner fa-spin text-primary"></i></div>
        </div>
        <div class="stat-card resolved">
            <div>
                <div id="admin-stat-resolved" class="stat-val">0</div>
                <div class="stat-lbl">Resolved Issues</div>
            </div>
            <div class="stat-icon"><i class="fas fa-check-circle text-secondary"></i></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-container">
            <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-chart-pie mr-2 text-primary"></i> Complaint Status Distribution</h4>
            <div style="position: relative; height: 260px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-chart-bar mr-2 text-secondary"></i> Distribution by Category</h4>
            <div style="position: relative; height: 260px;">
                <canvas id="categoryBarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly trend line -->
        <div class="col-md-8">
            <div class="chart-container" style="height: 100%;">
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-chart-line mr-2 text-info"></i> Complaint Activity Trends</h4>
                <div style="position: relative; height: 300px;">
                    <canvas id="trendLineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="col-md-4">
            <div class="card" style="height: 100%;">
                <div class="card-header">Quick Tools</div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('admin.complaints') }}" class="btn btn-primary w-100 py-3"><i class="fas fa-ticket-alt"></i> Manage Complaints</a>
                        <a href="{{ route('admin.users') }}" class="btn btn-dark w-100 py-3"><i class="fas fa-users"></i> Manage Users</a>
                        <a href="{{ route('admin.settings') }}" class="btn btn-secondary w-100 py-3"><i class="fas fa-sliders-h"></i> System Config</a>
                        <a href="{{ route('admin.system-health') }}" class="btn btn-outline-primary w-100 py-3"><i class="fas fa-heartbeat"></i> Diagnostic Check</a>
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
        // Initialize graphs & stats
        loadAdminDashboard();

        // 60s Auto refresh stats
        setInterval(function() {
            loadAdminDashboard();
        }, 60000);
    });
</script>
@endsection
