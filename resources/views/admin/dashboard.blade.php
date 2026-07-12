@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in-up">
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
            <div class="card border-0 shadow-sm rounded-4" style="height: 100%;">
                <div class="card-header bg-transparent border-0 py-4"><h5 class="font-weight-bold mb-0">Quick Tools</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('admin.complaints') }}" class="btn btn-primary w-100 py-3 font-weight-bold" style="border-radius: 12px;"><i class="fas fa-ticket-alt mr-2"></i> Manage Complaints</a>
                        <a href="{{ route('admin.users') }}" class="btn w-100 py-3 font-weight-bold" style="background: var(--dark); color: white; border-radius: 12px;"><i class="fas fa-users mr-2"></i> Manage Users</a>
                        <a href="{{ route('admin.settings') }}" class="btn w-100 py-3 font-weight-bold" style="background: var(--secondary); color: white; border-radius: 12px;"><i class="fas fa-sliders-h mr-2"></i> System Config</a>
                        <a href="{{ route('admin.system-health') }}" class="btn btn-outline-primary w-100 py-3 font-weight-bold" style="border-radius: 12px;"><i class="fas fa-heartbeat mr-2"></i> Diagnostic Check</a>
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
