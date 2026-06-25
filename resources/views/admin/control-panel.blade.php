@extends('layouts.admin')

@section('title', 'Admin Control Panel - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-cogs"></i> System Control Panel</h1>
    </div>

    <!-- Quick actions cards grid -->
    <div class="row">
        <!-- Ticket Management -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--primary);">
                <div class="card-body p-4">
                    <div class="mb-3 text-primary" style="font-size: 2.2rem;"><i class="fas fa-ticket-alt"></i></div>
                    <h4 class="font-weight-bold mb-2">Manage Complaints</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Review submissions, update statuses, assign admins, add comments, delete complaints.</p>
                    <a href="{{ route('admin.complaints') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Open Manager</a>
                </div>
            </div>
        </div>

        <!-- Users Management -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--dark);">
                <div class="card-body p-4">
                    <div class="mb-3 text-dark" style="font-size: 2.2rem;"><i class="fas fa-users"></i></div>
                    <h4 class="font-weight-bold mb-2">Manage Users</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Inspect user lists, switch user roles, toggle account active states, delete users.</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Open Manager</a>
                </div>
            </div>
        </div>

        <!-- Categories Settings -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--secondary);">
                <div class="card-body p-4">
                    <div class="mb-3 text-secondary" style="font-size: 2.2rem;"><i class="fas fa-folder-open"></i></div>
                    <h4 class="font-weight-bold mb-2">Manage Categories</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Configure category tags, descriptions, Font Awesome icons, color themes.</p>
                    <a href="{{ route('admin.categories') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Open Config</a>
                </div>
            </div>
        </div>

        <!-- Analytics charts -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--info);">
                <div class="card-body p-4">
                    <div class="mb-3 text-info" style="font-size: 2.2rem;"><i class="fas fa-chart-pie"></i></div>
                    <h4 class="font-weight-bold mb-2">Analytics & Trends</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Review hourly/weekly distribution graphs, audit assignee work rates, export filters.</p>
                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Open Analytics</a>
                </div>
            </div>
        </div>

        <!-- System settings -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--warning);">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning" style="font-size: 2.2rem;"><i class="fas fa-sliders-h"></i></div>
                    <h4 class="font-weight-bold mb-2">Site Configuration</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Modify site names, admin emails, paginations, file limits, maintenance modes.</p>
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Configure Settings</a>
                </div>
            </div>
        </div>

        <!-- System Diagnostics -->
        <div class="col-md-4 col-sm-6">
            <div class="card text-center" style="border-top: 4px solid var(--danger);">
                <div class="card-body p-4">
                    <div class="mb-3 text-danger" style="font-size: 2.2rem;"><i class="fas fa-heartbeat"></i></div>
                    <h4 class="font-weight-bold mb-2">System Diagnostics</h4>
                    <p class="text-muted mb-4" style="font-size: 0.85rem;">Check server disk allocation, memory limits, active PHP/MySQL versions, audit logs.</p>
                    <a href="{{ route('admin.system-health') }}" class="btn btn-outline-primary btn-sm w-100 py-2">Check Health</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
