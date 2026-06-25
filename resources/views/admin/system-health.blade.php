@extends('layouts.admin')

@section('title', 'System Health - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <h1 class="dashboard-section-title mb-4"><i class="fas fa-heartbeat"></i> System Diagnostics</h1>

    <div class="row">
        <!-- Parameters and allocation -->
        <div class="col-md-6">
            <div class="card mb-4" id="system-health-container">
                <div class="card-header">Server Environment</div>
                <div class="card-body">
                    <table class="table" style="border: 0;">
                        <tbody>
                            <tr>
                                <td style="border: 0; padding: 10px 0;"><span class="text-muted font-weight-bold">PHP Version</span></td>
                                <td style="border: 0; padding: 10px 0; text-align: right;" id="health-php-version">Loading...</td>
                            </tr>
                            <tr>
                                <td style="border: 0; padding: 10px 0;"><span class="text-muted font-weight-bold">MySQL Version</span></td>
                                <td style="border: 0; padding: 10px 0; text-align: right;" id="health-mysql-version">Loading...</td>
                            </tr>
                            <tr>
                                <td style="border: 0; padding: 10px 0;"><span class="text-muted font-weight-bold">Database Status</span></td>
                                <td style="border: 0; padding: 10px 0; text-align: right;"><span class="badge" style="background-color: var(--secondary);" id="health-db-status">Loading...</span></td>
                            </tr>
                            <tr>
                                <td style="border: 0; padding: 10px 0;"><span class="text-muted font-weight-bold">Storage Folder</span></td>
                                <td style="border: 0; padding: 10px 0; text-align: right;"><span class="badge" style="background-color: var(--secondary);" id="health-storage-status">Loading...</span></td>
                            </tr>
                            <tr>
                                <td style="border: 0; padding: 10px 0;"><span class="text-muted font-weight-bold">System Uptime</span></td>
                                <td style="border: 0; padding: 10px 0; text-align: right;" id="health-uptime">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Allocation progress bars -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Resource Allocations</div>
                <div class="card-body">
                    <!-- Disk Allocation -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold" style="font-size: 0.88rem;">Server Disk Space Allocation</span>
                            <span class="text-muted" id="health-disk-lbl" style="font-size: 0.85rem;">Checking...</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="health-disk-bar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- Memory Limit -->
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-bold" style="font-size: 0.88rem;">PHP Memory Usage Limit</span>
                            <span class="text-muted" id="health-mem-lbl" style="font-size: 0.85rem;">Checking...</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="health-mem-bar" style="width: 0%; background-color: var(--secondary);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit logs table -->
    <div class="card">
        <div class="card-header">Recent Audit Logs</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Responsible User</th>
                            <th>Action Actioned</th>
                            <th>Target Entity</th>
                            <th>IP Address</th>
                            <th>Date Occurred</th>
                        </tr>
                    </thead>
                    <tbody id="audit-logs-table-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="audit-logs-pagination" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Load diagnostics parameters
        loadSystemHealth();
        // Load audit logs list
        loadAuditLogs(1);
    });
</script>
@endsection
