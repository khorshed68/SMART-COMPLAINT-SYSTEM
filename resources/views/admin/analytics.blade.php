@extends('layouts.admin')

@section('title', 'System Analytics - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in-up">
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
        <div class="stat-card" style="border-left-color: #f1c40f;">
            <div>
                <div id="analytics-satisfaction" class="stat-val">0.0</div>
                <div class="stat-lbl">Avg Satisfaction Rating</div>
            </div>
            <div class="stat-icon"><i class="fas fa-star text-warning"></i></div>
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

    <div class="row mt-4">
        <!-- Satisfaction Rating Distribution (Doughnut Chart) -->
        <div class="col-md-6 mb-4">
            <div class="chart-container h-100">
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem;"><i class="fas fa-smile mr-2 text-warning"></i> Rating Distribution</h4>
                <div style="position: relative; height: 260px;">
                    <canvas id="satisfactionChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Category Satisfaction Ratings Table -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 mb-0">
                <div class="card-header bg-transparent border-0 py-4"><h5 class="font-weight-bold mb-0"><i class="fas fa-tags mr-2 text-primary"></i> Category Satisfaction Scores</h5></div>
                <div class="card-body p-0" style="max-height: 295px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="border-top-0">Category</th>
                                    <th class="border-top-0">Total Rated</th>
                                    <th class="border-top-0">Average Satisfaction</th>
                                </tr>
                            </thead>
                            <tbody id="category-satisfaction-body">
                                <tr>
                                    <td colspan="3" class="text-center text-muted p-4">Loading satisfaction scores...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignee performance list -->
    <div class="card border-0 shadow-sm rounded-4 mt-2 mb-5">
        <div class="card-header bg-transparent border-0 py-4"><h5 class="font-weight-bold mb-0">Administrator Performance Matrix</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="border-top-0">Admin Name</th>
                            <th class="border-top-0">Assigned Complaints</th>
                            <th class="border-top-0">Resolved Complaints</th>
                            <th class="border-top-0">Avg Resolution Hours</th>
                            <th class="border-top-0">Resolution Success Rate</th>
                            <th class="border-top-0">Customer Rating</th>
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
    let hourlyChart, weekdayChart, satisfactionChart;

    function loadAnalytics() {
        // Fetch overview details
        $.get('/api/admin/analytics/overview', function(data) {
            document.getElementById('analytics-today').textContent = data.complaints_today;
            document.getElementById('analytics-this-month').textContent = data.complaints_this_month;
            document.getElementById('analytics-satisfaction').textContent = data.avg_satisfaction.toFixed(1);
            
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
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted p-4">No assignee performance data available.</td></tr>';
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
                    <td>
                        ${perf.avg_rating !== null ? `
                            <div class="d-flex align-items-center gap-1">
                                <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                                <span class="font-weight-bold" style="font-size: 0.85rem;">${perf.avg_rating}</span>
                                <span class="text-muted" style="font-size: 0.72rem;">(${perf.rated_count})</span>
                            </div>
                        ` : '<span class="text-muted" style="font-size: 0.8rem;">Unrated</span>'}
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

        // Load satisfaction distribution & category ratings
        $.get('/api/admin/analytics/satisfaction', function(data) {
            renderSatisfactionChart(data.ratings);
            renderCategorySatisfactionTable(data.categories);
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

    function renderSatisfactionChart(ratings) {
        const ctx = document.getElementById('satisfactionChart');
        if (!ctx) return;

        if (satisfactionChart) satisfactionChart.destroy();

        const labels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
        const values = [ratings[1], ratings[2], ratings[3], ratings[4], ratings[5]];
        const backgroundColors = [
            '#e74c3c', // 1 Star
            '#e67e22', // 2 Stars
            '#f1c40f', // 3 Stars
            '#3498db', // 4 Stars
            '#2ecc71'  // 5 Stars
        ];

        satisfactionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Outfit', size: 12 },
                            usePointStyle: true
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    function renderCategorySatisfactionTable(categories) {
        const tbody = document.getElementById('category-satisfaction-body');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (categories.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted p-4">No categories rated yet.</td></tr>';
            return;
        }

        categories.forEach(cat => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="font-weight-bold">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: ${cat.color}; margin-right: 8px;"></span>
                    ${cat.name}
                </td>
                <td>${cat.rated_count} reviews</td>
                <td>
                    ${cat.avg_rating !== null ? `
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                            <span class="font-weight-bold" style="font-size: 0.85rem;">${cat.avg_rating} / 5</span>
                        </div>
                    ` : '<span class="text-muted" style="font-size: 0.8rem;">Unrated</span>'}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    $(document).ready(loadAnalytics);
</script>
@endsection
