@extends('layouts.app')

@section('title', 'Staff Dashboard - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in">
    <!-- Welcome Hero Banner -->
    <div class="welcome-banner" style="position: relative; background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #312e81 100%); overflow: hidden; padding: 40px 30px; border-radius: 18px; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2); margin-bottom: 30px;">
        <!-- Glowing background vectors -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 1;">
            <svg width="100%" height="100%">
                <defs>
                    <radialGradient id="bannerGlow" cx="70%" cy="40%" r="50%">
                        <stop offset="0%" stop-color="#4f46e5" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#4f46e5" stop-opacity="0" />
                    </radialGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#bannerGlow)" />
            </svg>
        </div>

        <div class="welcome-banner-content" style="position: relative; z-index: 2; max-width: 100%;">
            <h1 class="welcome-title" style="font-size: 2.1rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                Hello, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="welcome-subtitle" style="font-size: 1.05rem; opacity: 0.9; line-height: 1.6; margin-bottom: 0; color: #cbd5e1; max-width: 90%;">
                Welcome to your Staff Workspace. Below are the complaints assigned to you. Update progress and log resolution details in real-time.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row mb-4">
        <!-- Total Assigned -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="stat-card stat-total" style="background-color: var(--card-bg); border-left: 4px solid #3b82f6;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-blue">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <div class="stat-val">{{ $stats->total ?? 0 }}</div>
                        <div class="stat-lbl">Total Assigned</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="stat-card stat-pending" style="background-color: var(--card-bg); border-left: 4px solid #f59e0b;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="stat-val">{{ $stats->pending ?? 0 }}</div>
                        <div class="stat-lbl">Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="stat-card stat-progress" style="background-color: var(--card-bg); border-left: 4px solid #8b5cf6;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-purple">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <div class="stat-val">{{ $stats->in_progress ?? 0 }}</div>
                        <div class="stat-lbl">In Progress</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="stat-card stat-resolved" style="background-color: var(--card-bg); border-left: 4px solid #10b981;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-circle bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-val">{{ $stats->resolved ?? 0 }}</div>
                        <div class="stat-lbl">Resolved</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form id="filters-form" onsubmit="event.preventDefault(); applyStaffFilters();" class="row align-items-center">
                <div class="col-md-4" style="padding: 5px 10px;">
                    <select id="filter-status" class="form-select" onchange="applyStaffFilters()">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div class="col-md-4" style="padding: 5px 10px;">
                    <select id="filter-priority" class="form-select" onchange="applyStaffFilters()">
                        <option value="">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="col-md-4" style="padding: 5px 10px;">
                    <div style="position: relative;">
                        <input type="text" id="filter-search" class="form-control" placeholder="Search title or description..." style="padding-right: 40px;" onkeyup="debounce(applyStaffFilters, 400)()">
                        <i class="fas fa-search text-muted" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assigned Complaints Grid/List -->
    <div id="complaints-list-container" class="row">
        <!-- Loaded via AJAX -->
    </div>

    <!-- Pagination -->
    <div id="complaints-pagination-container" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    let currentStaffFilters = {};

    function applyStaffFilters() {
        const filters = {
            status: document.getElementById('filter-status').value,
            priority: document.getElementById('filter-priority').value,
            search: document.getElementById('filter-search').value
        };
        loadStaffComplaints(1, filters);
    }

    function loadStaffComplaints(page = 1, filters = {}) {
        const container = document.getElementById('complaints-list-container');
        if (!container) return;

        Spinner.show(container);
        currentStaffFilters = filters;
        filters.page = page;

        $.get('/api/staff/complaints', filters, function(response) {
            Spinner.hide(container);
            container.innerHTML = '';
            
            const complaints = response.data;
            if (complaints.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center p-5" style="background-color: var(--card-bg); border-radius: 12px; border: 1.5px solid var(--border-color);">
                        <i class="far fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">No assigned complaints found.</p>
                    </div>
                `;
                return;
            }

            const grid = document.createElement('div');
            grid.className = 'complaint-grid w-100';

            complaints.forEach(item => {
                const card = document.createElement('a');
                card.href = `/staff/complaints/${item.id}`;
                card.className = 'complaint-card';
                card.innerHTML = `
                    <div class="complaint-card-header">
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">#${item.id}</span>
                        <div class="d-flex gap-2">
                            ${BadgeHelper.getPriority(item.priority)}
                            ${BadgeHelper.getStatus(item.status)}
                        </div>
                    </div>
                    <h3 class="complaint-card-title">${item.title}</h3>
                    <p class="complaint-card-desc">${item.description}</p>
                    <div class="complaint-card-footer">
                        <div><i class="far fa-calendar-alt mr-1"></i> ${formatDate(item.created_at)}</div>
                        <div><i class="fas fa-tag mr-1"></i> ${item.category ? item.category.name : 'Other'}</div>
                    </div>
                `;
                grid.appendChild(card);
            });

            container.appendChild(grid);

            // Pagination Rendering
            Pagination.render(response, 'complaints-pagination-container', (pageNum) => {
                loadStaffComplaints(pageNum, currentStaffFilters);
            });
        });
    }

    $(document).ready(function() {
        // Load initial complaints
        loadStaffComplaints(1);

        // 30s auto-refresh
        setInterval(function() {
            const filters = {
                status: document.getElementById('filter-status').value,
                priority: document.getElementById('filter-priority').value,
                search: document.getElementById('filter-search').value
            };
            loadStaffComplaints(1, filters);
        }, 30000);
    });
</script>
@endsection
