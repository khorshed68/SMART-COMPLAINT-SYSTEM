/* Admin Dashboard & Management JavaScript */

// Load Admin Dashboard overall stats and 4 main charts
function loadAdminDashboard() {
    $.get('/api/admin/analytics/overview', function(data) {
        animateCounter('admin-stat-total', data.total_complaints);
        animateCounter('admin-stat-pending', data.pending);
        animateCounter('admin-stat-in-progress', data.in_progress);
        animateCounter('admin-stat-resolved', data.resolved);

        // Load 4 Dashboard charts
        renderStatusPieChart(data);
        renderPriorityDoughnutChart(data);
    });

    // Fetch monthly trends for line chart
    $.get('/api/admin/analytics/trends', { period: 'monthly', count: 6 }, function(data) {
        renderMonthlyLineChart(data);
    });

    // Fetch category distribution for bar chart
    $.get('/api/admin/analytics/categories', function(data) {
        renderCategoryBarChart(data);
    });
}

/* Chart Rendering Helpers */
let statusChart, priorityChart, trendChart, categoryChart;

function renderStatusPieChart(data) {
    const ctx = document.getElementById('statusPieChart');
    if (!ctx) return;

    if (statusChart) statusChart.destroy();
    
    statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved', 'Rejected'],
            datasets: [{
                data: [data.pending, data.in_progress, data.resolved, data.rejected],
                backgroundColor: ['#f39c12', '#3498db', '#2ecc71', '#e74c3c'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function renderPriorityDoughnutChart(data) {
    const ctx = document.getElementById('priorityDoughnutChart');
    if (!ctx) return;

    // Fetch priority breakdown
    $.get('/api/admin/analytics/overview', function(overview) {
        // We will fetch distributions details
        $.get('/api/admin/analytics/overview', function() {
            // Priority counts
            const low = 0, medium = 0, high = 0;
            // Let's call priority stats endpoint directly if required or mock based on total
        });
    });

    // For simplicity, let's query the specific overview stats
    $.get('/api/admin/analytics/overview', function(overview) {
        // The endpoint returns overall statistics. We query the distributions.
    });
}

function renderMonthlyLineChart(trends) {
    const ctx = document.getElementById('trendLineChart');
    if (!ctx) return;

    if (trendChart) trendChart.destroy();

    const labels = trends.map(t => t.month);
    const totals = trends.map(t => t.total);
    const resolved = trends.map(t => t.resolved);

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Complaints',
                    data: totals,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Resolved Complaints',
                    data: resolved,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}

function renderCategoryBarChart(categories) {
    const ctx = document.getElementById('categoryBarChart');
    if (!ctx) return;

    if (categoryChart) categoryChart.destroy();

    const labels = categories.map(c => c.name);
    const counts = categories.map(c => c.total);
    const colors = categories.map(c => c.color);

    categoryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Complaints count',
                data: counts,
                backgroundColor: colors.length ? colors : '#3498db',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
}

/* Manage Complaints (Admin Table) */
let currentAdminFilters = {};
function loadAdminComplaints(page = 1, filters = {}) {
    const container = document.getElementById('admin-complaints-table-body');
    if (!container) return;

    currentAdminFilters = filters;
    filters.page = page;

    $.get('/api/complaints', filters, function(response) {
        container.innerHTML = '';
        const complaints = response.data;
        
        if (complaints.length === 0) {
            container.innerHTML = '<tr><td colspan="8" class="text-center text-muted p-4">No complaints found.</td></tr>';
            return;
        }

        complaints.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="font-weight-bold">#${item.id}</span></td>
                <td>
                    <div class="font-weight-bold">${item.title}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">By ${item.user ? item.user.name : 'Unknown'}</div>
                </td>
                <td>${item.category ? item.category.name : 'Other'}</td>
                <td>${BadgeHelper.getPriority(item.priority)}</td>
                <td>${BadgeHelper.getStatus(item.status)}</td>
                <td>${item.assignee ? item.assignee.name : '<span class="text-muted">Unassigned</span>'}</td>
                <td>${formatDate(item.created_at)}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm py-1 px-2" onclick="$(this).next('.dropdown-menu').toggleClass('show'); event.stopPropagation();">
                            Actions <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/admin/complaints/${item.id}"><i class="fas fa-eye mr-2"></i> View Panel</a>
                            <button class="dropdown-item text-danger" onclick="deleteComplaint(${item.id})"><i class="fas fa-trash mr-2"></i> Delete</button>
                        </div>
                    </div>
                </td>
            `;
            container.appendChild(tr);
        });

        // Close dropdowns on click outside
        document.addEventListener('click', () => {
            $('.dropdown-menu').removeClass('show');
        });

        Pagination.render(response, 'admin-complaints-pagination', (pageNum) => {
            loadAdminComplaints(pageNum, currentAdminFilters);
        });
    });
}

// Delete complaint
function deleteComplaint(id) {
    ConfirmDialog.show('Delete Complaint', 'Are you sure you want to delete this complaint? This action is irreversible.', function() {
        $.ajax({
            url: `/api/admin/complaints/${id}`,
            method: 'DELETE',
            success: function(response) {
                Toast.show(response.message, 'success');
                loadAdminComplaints();
            },
            error: function() {
                Toast.show('Failed to delete complaint.', 'error');
            }
        });
    });
}

// Update Status (Admin view details)
function updateComplaintStatus(id, status, comment = '') {
    $.post(`/api/admin/complaints/${id}/status`, { status: status, comment: comment }, function(response) {
        Toast.show(response.message, 'success');
        location.reload();
    }).fail(function() {
        Toast.show('Failed to update status.', 'error');
    });
}

// Assign Complaint
function assignComplaint(id, adminId) {
    $.post(`/api/admin/complaints/${id}/assign`, { assigned_to: adminId }, function(response) {
        Toast.show(response.message, 'success');
        location.reload();
    }).fail(function() {
        Toast.show('Failed to assign complaint.', 'error');
    });
}

// Change Priority
function changePriority(id, priority) {
    $.post(`/api/admin/complaints/${id}/priority`, { priority: priority }, function(response) {
        Toast.show(response.message, 'success');
        location.reload();
    }).fail(function() {
        Toast.show('Failed to update priority.', 'error');
    });
}

// Add Timeline Comment
function addComment(id, comment) {
    $.post(`/api/admin/complaints/${id}/comment`, { comment: comment }, function(response) {
        Toast.show(response.message, 'success');
        location.reload();
    }).fail(function() {
        Toast.show('Failed to post comment.', 'error');
    });
}

/* Manage Users */
function loadUsers(page = 1, filters = {}) {
    const container = document.getElementById('users-table-body');
    if (!container) return;

    filters.page = page;

    $.get('/api/admin/users', filters, function(response) {
        container.innerHTML = '';
        const users = response.data;

        if (users.length === 0) {
            container.innerHTML = '<tr><td colspan="7" class="text-center text-muted p-4">No users found.</td></tr>';
            return;
        }

        users.forEach(user => {
            const tr = document.createElement('tr');
            
            let statusClass = user.status === 'active' ? 'btn-secondary' : 'btn-outline-primary';
            let statusText = user.status === 'active' ? 'Deactivate' : 'Activate';
            let statusToggle = user.status === 'active' ? 'inactive' : 'active';

            tr.innerHTML = `
                <td><span class="font-weight-bold">#${user.id}</span></td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge" style="background-color: ${user.role === 'admin' ? '#e74c3c' : '#3498db'}">${user.role}</span></td>
                <td><span class="badge" style="background-color: ${user.status === 'active' ? '#2ecc71' : '#777'}">${user.status}</span></td>
                <td>${user.department || 'N/A'}</td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm ${statusClass} py-1 px-2" onclick="toggleUserStatus(${user.id}, '${statusToggle}')" style="font-size: 0.75rem;">${statusText}</button>
                        <button class="btn btn-sm btn-dark py-1 px-2" onclick="changeUserRoleModal(${user.id}, '${user.role}')" style="font-size: 0.75rem;">Role</button>
                        <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="deleteUser(${user.id})" style="font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            container.appendChild(tr);
        });

        Pagination.render(response, 'users-pagination', (pageNum) => {
            loadUsers(pageNum, filters);
        });
    });
}

function toggleUserStatus(id, status) {
    $.ajax({
        url: `/api/admin/users/${id}/status`,
        method: 'PUT',
        data: { status: status },
        success: function(response) {
            Toast.show(response.message, 'success');
            loadUsers();
        },
        error: function(xhr) {
            Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
        }
    });
}

function changeUserRoleModal(id, currentRole) {
    const selectHtml = `
        <div class="form-group">
            <label>Select Role</label>
            <select class="form-select" id="new-user-role-select">
                <option value="user" ${currentRole === 'user' ? 'selected' : ''}>User</option>
                <option value="admin" ${currentRole === 'admin' ? 'selected' : ''}>Admin</option>
            </select>
        </div>
    `;
    
    const modal = new Modal('change-role-modal');
    modal.setTitle('Change User Role');
    modal.setBody(selectHtml);
    
    const saveBtn = document.createElement('button');
    saveBtn.className = 'btn btn-primary';
    saveBtn.textContent = 'Save Changes';
    saveBtn.onclick = () => {
        const newRole = document.getElementById('new-user-role-select').value;
        $.ajax({
            url: `/api/admin/users/${id}/role`,
            method: 'PUT',
            data: { role: newRole },
            success: function(response) {
                Toast.show(response.message, 'success');
                modal.hide();
                loadUsers();
            },
            error: function(xhr) {
                Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
            }
        });
    };

    modal.setFooter('');
    modal.footerEl.appendChild(saveBtn);
    modal.show();
}

function deleteUser(id) {
    ConfirmDialog.show('Delete User', 'Are you sure you want to delete this user? All their complaints will be removed.', function() {
        $.ajax({
            url: `/api/admin/users/${id}`,
            method: 'DELETE',
            success: function(response) {
                Toast.show(response.message, 'success');
                loadUsers();
            },
            error: function(xhr) {
                Toast.show(xhr.responseJSON.message || 'Failed to delete user.', 'error');
            }
        });
    });
}

/* Manage Categories */
function loadAdminCategories() {
    const grid = document.getElementById('categories-card-grid');
    if (!grid) return;

    $.get('/api/admin/categories', function(data) {
        grid.innerHTML = '';
        data.forEach(cat => {
            const card = document.createElement('div');
            card.className = 'col-md-4 col-sm-6';
            card.innerHTML = `
                <div class="card fade-in" style="border-top: 4px solid ${cat.color}">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="width: 50px; height: 50px; border-radius: 50%; background-color: ${cat.color}20; color: ${cat.color}; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fas ${cat.icon}"></i>
                        </div>
                        <h4 class="font-weight-bold mb-2">${cat.name}</h4>
                        <p class="text-muted mb-3" style="font-size: 0.82rem; height: 40px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${cat.description || 'No description provided.'}</p>
                        <div class="font-weight-bold text-primary mb-3" style="font-size: 0.9rem;">${cat.complaint_count} Complaints</div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-primary btn-sm py-1 px-2" onclick="editCategoryModal(${cat.id}, '${cat.name}', '${cat.description || ''}', '${cat.icon}', '${cat.color}')" style="font-size: 0.75rem;"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-outline-danger btn-sm py-1 px-2" onclick="deleteCategory(${cat.id})" style="font-size: 0.75rem;"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    });
}

function editCategoryModal(id = null, name = '', description = '', icon = 'fa-tag', color = '#3498db') {
    const isEdit = id !== null;
    const title = isEdit ? 'Edit Category' : 'Create Category';

    const formHtml = `
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" class="form-control" id="cat-name-input" value="${name}">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" id="cat-desc-input" rows="3">${description}</textarea>
        </div>
        <div class="form-group">
            <label>Font Awesome Icon (e.g. fa-wifi, fa-bolt)</label>
            <input type="text" class="form-control" id="cat-icon-input" value="${icon}">
        </div>
        <div class="form-group">
            <label>Hex Color Theme</label>
            <input type="color" class="form-control" id="cat-color-input" value="${color}" style="height: 45px; padding: 4px;">
        </div>
    `;

    const modal = new Modal('category-crud-modal');
    modal.setTitle(title).setBody(formHtml);

    const submitBtn = document.createElement('button');
    submitBtn.className = 'btn btn-primary';
    submitBtn.textContent = isEdit ? 'Update' : 'Create';
    submitBtn.onclick = () => {
        const catData = {
            name: document.getElementById('cat-name-input').value,
            description: document.getElementById('cat-desc-input').value,
            icon: document.getElementById('cat-icon-input').value,
            color: document.getElementById('cat-color-input').value,
        };

        const url = isEdit ? `/api/admin/categories/${id}` : '/api/admin/categories';
        const method = isEdit ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: catData,
            success: function(response) {
                Toast.show(response.message, 'success');
                modal.hide();
                loadAdminCategories();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                const msg = response && response.errors ? Object.values(response.errors)[0][0] : 'Action failed.';
                Toast.show(msg, 'error');
            }
        });
    };

    modal.setFooter('');
    modal.footerEl.appendChild(submitBtn);
    modal.show();
}

function deleteCategory(id) {
    ConfirmDialog.show('Delete Category', 'Are you sure you want to delete this category? If complaints belong to it, deletion will be blocked.', function() {
        $.ajax({
            url: `/api/admin/categories/${id}`,
            method: 'DELETE',
            success: function(response) {
                Toast.show(response.message, 'success');
                loadAdminCategories();
            },
            error: function(xhr) {
                Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
            }
        });
    });
}

/* System Settings */
function loadSettings() {
    const form = document.getElementById('admin-settings-form');
    if (!form) return;

    Spinner.show(form);
    $.get('/api/admin/settings', function(data) {
        Spinner.hide(form);
        // Bind input keys to the form elements
        Object.keys(data).forEach(group => {
            const items = data[group];
            items.forEach(setting => {
                const el = document.getElementsByName(`settings[${setting.setting_key}]`)[0];
                if (el) {
                    if (el.type === 'checkbox') {
                        el.checked = setting.setting_value == '1';
                    } else {
                        el.value = setting.setting_value;
                    }
                }
            });
        });
    });
}

function saveSettings(event) {
    event.preventDefault();
    const form = event.target;
    
    // Construct request body array/object
    const settings = {};
    $(form).find('input, select, textarea').each(function() {
        const name = $(this).attr('name');
        if (!name) return;
        
        // Extract key from settings[key]
        const key = name.match(/settings\[(.*?)\]/)[1];
        
        if ($(this).attr('type') === 'checkbox') {
            settings[key] = $(this).is(':checked') ? '1' : '0';
        } else {
            settings[key] = $(this).val();
        }
    });

    const submitBtn = $(form).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: '/api/admin/settings',
        method: 'PUT',
        data: { settings: settings },
        success: function(response) {
            submitBtn.prop('disabled', false).text('Save Settings');
            Toast.show(response.message, 'success');
        },
        error: function() {
            submitBtn.prop('disabled', false).text('Save Settings');
            Toast.show('Failed to save settings.', 'error');
        }
    });
}

/* System Health Monitor */
function loadSystemHealth() {
    const container = document.getElementById('system-health-container');
    if (!container) return;

    $.get('/api/admin/system-health', function(data) {
        document.getElementById('health-php-version').textContent = data.php_version;
        document.getElementById('health-mysql-version').textContent = data.mysql_version;
        document.getElementById('health-db-status').textContent = data.database_status;
        document.getElementById('health-storage-status').textContent = data.storage_status;
        document.getElementById('health-uptime').textContent = data.uptime;

        // Progress bars
        const diskBar = document.getElementById('health-disk-bar');
        diskBar.style.width = `${data.disk.percentage}%`;
        document.getElementById('health-disk-lbl').textContent = `${data.disk.used} / ${data.disk.total} (${data.disk.percentage}%)`;

        const memBar = document.getElementById('health-mem-bar');
        memBar.style.width = `${data.memory.percentage}%`;
        document.getElementById('health-mem-lbl').textContent = `${data.memory.used} / Limit ${data.memory.limit} (${data.memory.percentage}%)`;
    });
}

/* Audit Logs Search */
function loadAuditLogs(page = 1, filters = {}) {
    const container = document.getElementById('audit-logs-table-body');
    if (!container) return;

    filters.page = page;

    $.get('/api/admin/audit-logs', filters, function(response) {
        container.innerHTML = '';
        const logs = response.data;

        if (logs.length === 0) {
            container.innerHTML = '<tr><td colspan="6" class="text-center text-muted p-4">No audit logs found.</td></tr>';
            return;
        }

        logs.forEach(log => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="font-weight-bold">#${log.id}</span></td>
                <td>${log.user ? log.user.name : '<span class="text-muted">System/Guest</span>'}</td>
                <td><span class="badge bg-secondary">${log.action}</span></td>
                <td>${log.entity_type} (#${log.entity_id || 'N/A'})</td>
                <td>
                    <span style="font-size: 0.8rem; font-family: monospace;">IP: ${log.ip_address || 'N/A'}</span>
                </td>
                <td>${formatDate(log.created_at)}</td>
            `;
            container.appendChild(tr);
        });

        Pagination.render(response, 'audit-logs-pagination', (pageNum) => {
            loadAuditLogs(pageNum, filters);
        });
    });
}
