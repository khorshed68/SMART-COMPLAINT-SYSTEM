@extends('layouts.app')

@section('title', 'Assigned Complaints - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in">
    <!-- Header Card -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); margin-bottom: 30px;">
        <div class="card-body p-4 text-white">
            <h1 class="font-weight-bold mb-1" style="font-size: 1.8rem;"><i class="fas fa-tasks text-warning mr-2"></i> Assigned Complaints</h1>
            <p class="mb-0 text-muted" style="font-size: 0.95rem; color: #94a3b8 !important;">Review and update complaints assigned to your department in real-time.</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; margin-bottom: 30px !important;">
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
