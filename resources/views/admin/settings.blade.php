@extends('layouts.admin')

@section('title', 'System Settings - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in" style="max-width: 800px; margin: 0 auto;">
    <h1 class="dashboard-section-title mb-4"><i class="fas fa-sliders-h"></i> System Settings</h1>

    <div class="card">
        <div class="card-body">
            <form id="admin-settings-form" onsubmit="saveSettings(event)">
                <!-- General Settings -->
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem; border-bottom: 1.5px solid var(--border); padding-bottom: 8px; color: var(--primary);">General Parameters</h4>
                <div class="form-group">
                    <label>Website Name</label>
                    <input type="text" name="settings[site_name]" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label>Site Administrator Email</label>
                    <input type="email" name="settings[site_email]" class="form-control" required>
                </div>

                <!-- Complaints Settings -->
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem; border-bottom: 1.5px solid var(--border); padding-bottom: 8px; color: var(--primary);">Complaints Configuration</h4>
                <div class="form-group">
                    <label>Complaints Per Page (Listing views)</label>
                    <input type="number" name="settings[complaints_per_page]" class="form-control" min="5" max="100" required>
                </div>
                <div class="form-group mb-4">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="settings[enable_auto_assignment]" value="1" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                        Enable Auto-Assignment (Distribute complaints automatically among active admins)
                    </label>
                </div>

                <!-- Email Notifications -->
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem; border-bottom: 1.5px solid var(--border); padding-bottom: 8px; color: var(--primary);">Email Settings</h4>
                <div class="form-group mb-4">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="settings[enable_email_notifications]" value="1" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                        Enable Email Notifications (Sends emails on status updates, submission confirmation, assignments)
                    </label>
                </div>

                <!-- System & Limits -->
                <h4 class="font-weight-bold mb-3" style="font-size: 1rem; border-bottom: 1.5px solid var(--border); padding-bottom: 8px; color: var(--primary);">System Limits & Modes</h4>
                <div class="row">
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label>Maximum File Upload Size (in bytes)</label>
                            <input type="number" name="settings[max_file_size]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 10px;">
                        <div class="form-group">
                            <label>Allowed File Formats (comma separated)</label>
                            <input type="text" name="settings[allowed_file_types]" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="settings[maintenance_mode]" value="1" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                        Enable Maintenance Mode (Restricts access to clients during updates)
                    </label>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(loadSettings);
</script>
@endsection
