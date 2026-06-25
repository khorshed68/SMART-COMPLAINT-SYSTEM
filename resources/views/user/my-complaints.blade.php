@extends('layouts.app')

@section('title', 'My Complaints - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-list-alt"></i> My Complaint History</h1>
        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Submit New</a>
    </div>

    <!-- Filters Row -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form id="filters-form" onsubmit="event.preventDefault(); applyFilters();" class="row align-items-center">
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-status" class="form-select" onchange="applyFilters()">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-priority" class="form-select" onchange="applyFilters()">
                        <option value="">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-category" class="form-select" onchange="applyFilters()">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding: 5px 10px;">
                    <div style="position: relative;">
                        <input type="text" id="filter-search" class="form-control" placeholder="Search title or description..." style="padding-right: 40px;" onkeyup="debounce(applyFilters, 400)()">
                        <i class="fas fa-search text-muted" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Complaints Grid List Container -->
    <div id="complaints-list-container" class="row">
        <!-- Loaded via AJAX -->
    </div>

    <!-- Pagination Container -->
    <div id="complaints-pagination-container"></div>
</div>
@endsection

@section('scripts')
<script>
    function applyFilters() {
        const filters = {
            status: document.getElementById('filter-status').value,
            priority: document.getElementById('filter-priority').value,
            category_id: document.getElementById('filter-category').value,
            search: document.getElementById('filter-search').value
        };
        loadComplaints(1, filters);
    }

    $(document).ready(function() {
        // Load categories selector
        loadCategories('filter-category');

        // Load complaints initially
        loadComplaints(1);

        // 30s auto-refresh
        setInterval(function() {
            const filters = {
                status: document.getElementById('filter-status').value,
                priority: document.getElementById('filter-priority').value,
                category_id: document.getElementById('filter-category').value,
                search: document.getElementById('filter-search').value
            };
            loadComplaints(1, filters);
        }, 30000);
    });
</script>
@endsection
