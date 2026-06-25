@extends('layouts.admin')

@section('title', 'Manage Complaints - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-ticket-alt"></i> Complaints Management</h1>
        <div class="d-flex gap-2">
            <button onclick="exportData('csv')" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-csv"></i> Export CSV</button>
            <button onclick="exportData('json')" class="btn btn-dark btn-sm"><i class="fas fa-file-code"></i> Export JSON</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form id="admin-filters-form" onsubmit="event.preventDefault(); applyAdminFilters();" class="row align-items-center">
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-status" class="form-select" onchange="applyAdminFilters()">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-priority" class="form-select" onchange="applyAdminFilters()">
                        <option value="">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-category" class="form-select" onchange="applyAdminFilters()">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <div style="position: relative;">
                        <input type="text" id="filter-search" class="form-control" placeholder="Search title or user..." style="padding-right: 40px;" onkeyup="debounce(applyAdminFilters, 400)()">
                        <i class="fas fa-search text-muted" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Complaint Details</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Date Filed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="admin-complaints-table-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="admin-complaints-pagination" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    function applyAdminFilters() {
        const filters = {
            status: document.getElementById('filter-status').value,
            priority: document.getElementById('filter-priority').value,
            category_id: document.getElementById('filter-category').value,
            search: document.getElementById('filter-search').value
        };
        loadAdminComplaints(1, filters);
    }

    function exportData(format) {
        const status = document.getElementById('filter-status').value;
        const priority = document.getElementById('filter-priority').value;
        const category_id = document.getElementById('filter-category').value;
        const search = document.getElementById('filter-search').value;

        let query = `?format=${format}`;
        if (status) query += `&status=${status}`;
        if (priority) query += `&priority=${priority}`;
        if (category_id) query += `&category_id=${category_id}`;
        if (search) query += `&search=${search}`;

        window.location.href = `/api/admin/analytics/export${query}`;
    }

    $(document).ready(function() {
        // Load filter dropdown categories
        loadCategories('filter-category');

        // Load complaints list
        loadAdminComplaints(1);

        // 60s auto-refresh
        setInterval(function() {
            const filters = {
                status: document.getElementById('filter-status').value,
                priority: document.getElementById('filter-priority').value,
                category_id: document.getElementById('filter-category').value,
                search: document.getElementById('filter-search').value
            };
            loadAdminComplaints(1, filters);
        }, 60000);
    });
</script>
@endsection
