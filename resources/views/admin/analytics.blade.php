@extends('layouts.admin')

@section('title', 'System Analytics - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-chart-pie"></i> Analytics Dashboard</h1>
    </div>

    <!-- Overview Stats Block -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: var(--primary);">
            <div>
                <div id="analytics-avg-hours" class="stat-val">0</div>
                <div class="stat-lbl">Avg Resolution Hours</div>
            </div>
            <div class="stat-icon"><i class="fas fa-bolt text-primary"></i></div>
        </div>
        <div class="stat-card" style="border-left-color: var(--secondary);">
            <div style="flex-grow: 1;">
                <div id="analytics-rate" class="stat-val">0%</div>
                <div class="stat-lbl">Resolution Rate</div>
            </div>
            <div class="stat-icon"><i class="fas fa-percentage text-secondary"></i></div>
        </div>
        <div class="stat-card" style="border-left-color: var(--warning);">
            <div>
                <div id="analytics-today" class="stat-val">0</div>
                <div class="stat-lbl">Filed Today</div>
            </div>
            <div class="stat-icon"><i class="fas fa-calendar-day text-warning"></i></div>
        </div>
        <div class="stat-card" style="border-left-color: var(--danger);">
            <div>
                <div id="analytics-this-month" class="stat-val">0</div>
                <div class="stat-lbl">Filed This Month</div>
            </div>
            <div class="stat-icon"><i class="fas fa-calendar-alt text-danger"></i></div>
        </div>
    </div>

    <!-- Graphs grid -->
    <div class="charts-grid">
        <div class="chart-container">
            <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-history mr-2 text-primary"></i> Hourly Distribution</h4>
            <div style="position: relative; height: 260px;">
                <canvas id="hourlyDistChart"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-calendar-week mr-2 text-secondary"></i> Weekday Distribution</h4>
            <div style="position: relative; height: 260px;">
                <canvas id="weekdayDistChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Assignee performance list -->
    <div class="card mt-4">
        <div class="card-header">Administrator Performance Matrix</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Admin Name</th>
                            <th>Assigned Complaints</th>
                            <th>Resolved Complaints</th>
                            <th>Avg Resolution Hours</th>
                            <th>Resolution Success Rate</th>
                        </tr>
                    </thead>
                    <tbody id="assignee-performance-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let hourlyChart, weekdayChart;

    function loadAnalytics() {
        // Fetch overview details
        $.get('/api/admin/analytics/overview', function(data) {
            document.getElementById('analytics-today').textContent = data.complaints_today;
            document.getElementById('analytics-this-month').textContent = data.complaints_this_month;
            
            const rate = data.total_complaints > 0 ? (data.resolved / data.total_complaints) * 100 : 0;
            document.getElementById('analytics-rate').textContent = `${rate.toFixed(1)}%`;
        });

        // Fetch resolution averages
        $.get('/api/admin/analytics/resolution', function(data) {
            document.getElementById('analytics-avg-hours').textContent = `${data.resolution_time.avg_hours}h`;

            // Render assignee table
            const tbody = document.getElementById('assignee-performance-body');
            tbody.innerHTML = '';
            
            if (data.assignee_performance.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-4">No assignee performance data available.</td></tr>';
                return;
            }

            data.assignee_performance.forEach(perf => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="font-weight-bold">${perf.name}</td>
                    <td>${perf.assigned}</td>
                    <td>${perf.resolved}</td>
                    <td>${perf.avg_hours} hours</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width: 100px;">
                                <div class="progress-bar" style="width: ${perf.rate}%; background-color: var(--secondary);"></div>
                            </div>
                            <span class="font-weight-bold" style="font-size: 0.85rem;">${perf.rate}%</span>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });

        // Load hourly distribution
        $.get('/api/admin/analytics/distribution', { type: 'hourly' }, function(dist) {
            renderHourlyChart(dist);
        });

        // Load weekday distribution
        $.get('/api/admin/analytics/distribution', { type: 'weekday' }, function(dist) {
            renderWeekdayChart(dist);
        });
    }

    function renderHourlyChart(dist) {
        const ctx = document.getElementById('hourlyDistChart');
        if (!ctx) return;

        if (hourlyChart) hourlyChart.destroy();

        const labels = Array.from({length: 24}, (_, i) => `${i}:00`);
        
        hourlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Complaints',
                    data: dist,
                    backgroundColor: 'rgba(52, 152, 219, 0.75)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    function renderWeekdayChart(dist) {
        const ctx = document.getElementById('weekdayDistChart');
        if (!ctx) return;

        if (weekdayChart) weekdayChart.destroy();

        const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        weekdayChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Complaints count',
                    data: dist,
                    borderColor: 'var(--secondary)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    $(document).ready(loadAnalytics);
</script>
@endsection
