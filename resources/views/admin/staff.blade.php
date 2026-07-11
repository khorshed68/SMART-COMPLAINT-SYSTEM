@extends('layouts.admin')

@section('title', 'Manage Staff - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title"><i class="fas fa-tools"></i> Staff Management</h1>
        <button onclick="openAddStaffModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Staff Member</button>
    </div>

    <!-- Filters Row -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form id="staff-filters-form" onsubmit="event.preventDefault(); applyStaffFilters();" class="row align-items-center">
                <div class="col-md-3" style="padding: 5px 10px;">
                    <select id="filter-staff-status" class="form-select" onchange="applyStaffFilters()">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-9" style="padding: 5px 10px;">
                    <div style="position: relative;">
                        <input type="text" id="filter-staff-search" class="form-control" placeholder="Search by name, email, or department specialty..." style="padding-right: 40px;" onkeyup="debounce(applyStaffFilters, 400)()">
                        <i class="fas fa-search text-muted" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Specialty Department</th>
                            <th>Active Tickets</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staff-table-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="staff-pagination" class="mt-4"></div>
</div>
@endsection

@section('scripts')
<script>
    let currentStaffFilters = {};

    function applyStaffFilters() {
        currentStaffFilters = {
            status: document.getElementById('filter-staff-status').value,
            search: document.getElementById('filter-staff-search').value
        };
        loadStaffList(1, currentStaffFilters);
    }

    function loadStaffList(page = 1, filters = {}) {
        const container = document.getElementById('staff-table-body');
        if (!container) return;

        filters.page = page;

        $.get('/api/admin/staff-list', filters, function(response) {
            container.innerHTML = '';
            const staffMembers = response.data;

            if (staffMembers.length === 0) {
                container.innerHTML = '<tr><td colspan="7" class="text-center text-muted p-4">No staff members found.</td></tr>';
                return;
            }

            staffMembers.forEach(staff => {
                const tr = document.createElement('tr');
                
                let badgeColor = '#777';
                if (staff.status === 'active') badgeColor = '#2ecc71';
                else if (staff.status === 'pending') badgeColor = '#f39c12';

                const activeTickets = staff.assigned_complaints_count || 0;
                const ticketBadgeColor = activeTickets > 0 ? '#ef4444' : '#10b981';

                let actionButtons = '';
                if (staff.status === 'pending') {
                    actionButtons = `
                        <button class="btn btn-sm btn-outline-success py-1 px-2 font-weight-bold" onclick="toggleStaffStatus(${staff.id}, 'active')" style="font-size: 0.75rem;">Approve</button>
                        <button class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="toggleStaffStatus(${staff.id}, 'inactive')" style="font-size: 0.75rem;">Reject</button>
                    `;
                } else if (staff.status === 'active') {
                    actionButtons = `
                        <button class="btn btn-sm btn-secondary py-1 px-2" onclick="toggleStaffStatus(${staff.id}, 'inactive')" style="font-size: 0.75rem;">Deactivate</button>
                    `;
                } else {
                    actionButtons = `
                        <button class="btn btn-sm btn-outline-success py-1 px-2" onclick="toggleStaffStatus(${staff.id}, 'active')" style="font-size: 0.75rem;">Activate</button>
                    `;
                }

                tr.innerHTML = `
                    <td><span class="font-weight-bold">#${staff.id}</span></td>
                    <td>${staff.name}</td>
                    <td>
                        <div style="font-size: 0.9rem; font-weight: 500;">${staff.email}</div>
                        <div class="text-muted" style="font-size: 0.78rem;">${staff.phone || 'No Phone Record'}</div>
                    </td>
                    <td><span class="badge" style="background-color: var(--secondary);">${staff.department || 'Other'}</span></td>
                    <td>
                        <span class="badge text-white" style="background-color: ${ticketBadgeColor}; font-size: 0.78rem; padding: 4px 8px; border-radius: 20px;">
                            ${activeTickets} Active
                        </span>
                    </td>
                    <td><span class="badge text-capitalize" style="background-color: ${badgeColor}">${staff.status}</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            ${actionButtons}
                            <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="deleteStaff(${staff.id})" style="font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;
                container.appendChild(tr);
            });

            Pagination.render(response, 'staff-pagination', (pageNum) => {
                loadStaffList(pageNum, filters);
            });
        });
    }

    function toggleStaffStatus(id, status) {
        $.ajax({
            url: `/api/admin/users/${id}/status`,
            method: 'PUT',
            data: { status: status },
            success: function(response) {
                Toast.show(response.message, 'success');
                loadStaffList();
            },
            error: function(xhr) {
                Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
            }
        });
    }

    function deleteStaff(id) {
        ConfirmDialog.show('Delete Staff Account', 'Are you sure you want to delete this staff member? This is irreversible.', function() {
            $.ajax({
                url: `/api/admin/users/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Toast.show(response.message, 'success');
                    loadStaffList();
                },
                error: function(xhr) {
                    Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
                }
            });
        });
    }

    function openAddStaffModal() {
        const formHtml = `
            <form id="add-staff-form" onsubmit="submitCreateStaff(event)" autocomplete="off">
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Sheikh Khorshed" required>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="electrician@complaint.system" required>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="017XXXXXXXX" required>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Department Specialty</label>
                    <select name="department" class="form-select" required>
                        <option value="">Select Department</option>
                        <option value="Maintenance & Plumbing">Maintenance & Plumbing</option>
                        <option value="Electrical Department">Electrical Department</option>
                        <option value="IT & Network Services">IT & Network Services</option>
                        <option value="Carpentry & Infrastructure">Carpentry & Infrastructure</option>
                        <option value="Housekeeping & Cleaning">Housekeeping & Cleaning</option>
                        <option value="Administration & Security">Administration & Security</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Initial Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" onclick="Modal.hideCurrent()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-staff">Create Account</button>
                </div>
            </form>
        `;

        const modal = new Modal('add-staff-modal');
        modal.setTitle('Add Staff Member');
        modal.setBody(formHtml);
        modal.setFooter('');
        modal.show();
    }

    function submitCreateStaff(event) {
        event.preventDefault();
        const form = event.target;
        const formData = $(form).serialize();
        const saveBtn = document.getElementById('btn-save-staff');
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

        $.ajax({
            url: '/api/admin/staff',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Create Account';
                if (response.success) {
                    Toast.show(response.message, 'success');
                    Modal.hideCurrent();
                    loadStaffList();
                }
            },
            error: function(xhr) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Create Account';
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to create staff member.';
                Toast.show(msg, 'error');
            }
        });
    }

    $(document).ready(function() {
        loadStaffList(1);
    });
</script>
@endsection
