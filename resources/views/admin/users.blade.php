@extends('layouts.admin')

@section('title', 'Manage Users - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in-up">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-users text-primary mr-2"></i> Users Management</h1>
    </div>

    <!-- Filters Row -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <form id="users-filters-form" onsubmit="event.preventDefault(); applyUserFilters();" class="row align-items-center">
                <div class="col-md-4" style="padding: 5px 10px;">
                    <select id="filter-user-status" class="form-select border-0 bg-light" onchange="applyUserFilters()">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-8" style="padding: 5px 10px;">
                    <div style="position: relative;">
                        <input type="text" id="filter-user-search" class="form-control border-0 bg-light" placeholder="Search by name or email..." style="padding-right: 40px;" onkeyup="debounce(applyUserFilters, 400)()">
                        <i class="fas fa-search text-muted" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-none bg-transparent">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="users-pagination" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    function applyUserFilters() {
        const filters = {
            status: document.getElementById('filter-user-status').value,
            search: document.getElementById('filter-user-search').value
        };
        loadUsers(1, filters);
    }

    $(document).ready(function() {
        // Load initial users
        loadUsers(1);
    });
</script>
@endsection
