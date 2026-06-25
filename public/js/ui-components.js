/* Reusable UI Components & Helpers */

// Toast Alerts
class Toast {
    static show(message, type = 'info', duration = 3000) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        let icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        if (type === 'error') icon = 'fa-exclamation-circle';
        if (type === 'warning') icon = 'fa-exclamation-triangle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <div class="toast-message">${message}</div>
        `;
        
        container.appendChild(toast);
        
        // Trigger reflow for slide animation
        toast.offsetHeight;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    }
}

// Modal Component
class Modal {
    constructor(elementId) {
        this.modalEl = document.getElementById(elementId);
        if (!this.modalEl) {
            // Create modal elements dynamically if not present
            this.modalEl = document.createElement('div');
            this.modalEl.id = elementId;
            this.modalEl.className = 'modal';
            this.modalEl.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="${elementId}-title">Modal Title</h5>
                            <button type="button" class="close-btn" onclick="document.getElementById('${elementId}').style.display='none'">&times;</button>
                        </div>
                        <div class="modal-body" id="${elementId}-body"></div>
                        <div class="modal-footer" id="${elementId}-footer"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(this.modalEl);
        }
        
        this.titleEl = document.getElementById(`${elementId}-title`);
        this.bodyEl = document.getElementById(`${elementId}-body`);
        this.footerEl = document.getElementById(`${elementId}-footer`);
    }

    show() {
        this.modalEl.style.display = 'block';
        // Add class for scale anim
        setTimeout(() => {
            this.modalEl.classList.add('show');
        }, 10);
    }

    hide() {
        this.modalEl.classList.remove('show');
        setTimeout(() => {
            this.modalEl.style.display = 'none';
        }, 150);
    }

    setTitle(text) {
        if (this.titleEl) this.titleEl.textContent = text;
        return this;
    }

    setBody(html) {
        if (this.bodyEl) {
            if (typeof html === 'string') {
                this.bodyEl.innerHTML = html;
            } else {
                this.bodyEl.innerHTML = '';
                this.bodyEl.appendChild(html);
            }
        }
        return this;
    }

    setFooter(html) {
        if (this.footerEl) {
            if (typeof html === 'string') {
                this.footerEl.innerHTML = html;
            } else {
                this.footerEl.innerHTML = '';
                this.footerEl.appendChild(html);
            }
        }
        return this;
    }
}

// Confirm Dialog
class ConfirmDialog {
    static show(title, message, onConfirm) {
        const modal = new Modal('confirm-dialog-modal');
        modal.setTitle(title);
        modal.setBody(`<p>${message}</p>`);
        
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-dark';
        cancelBtn.textContent = 'Cancel';
        cancelBtn.onclick = () => modal.hide();

        const confirmBtn = document.createElement('button');
        confirmBtn.className = 'btn btn-danger';
        confirmBtn.textContent = 'Confirm';
        confirmBtn.onclick = () => {
            onConfirm();
            modal.hide();
        };

        modal.setFooter('');
        modal.footerEl.appendChild(cancelBtn);
        modal.footerEl.appendChild(confirmBtn);
        
        modal.show();
    }
}

// Drag and Drop Upload Component
class FileUploadComponent {
    constructor(zoneId, inputId, previewId) {
        this.zone = document.getElementById(zoneId);
        this.input = document.getElementById(inputId);
        this.preview = document.getElementById(previewId);
        
        if (!this.zone || !this.input) return;

        this.init();
    }

    init() {
        // Click to open file dialog
        this.zone.addEventListener('click', () => this.input.click());

        // File selection event
        this.input.addEventListener('change', (e) => this.handleFiles(e.target.files));

        // Drag events
        ['dragenter', 'dragover'].forEach(eventName => {
            this.zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                this.zone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                this.zone.classList.remove('dragover');
            }, false);
        });

        this.zone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            this.input.files = files;
            this.handleFiles(files);
        }, false);
    }

    handleFiles(files) {
        if (!files.length) return;
        const file = files[0];
        
        // Show basic preview details
        if (this.preview) {
            this.preview.innerHTML = `
                <div class="d-flex align-items-center mt-3 p-3" style="background: #f8f9fa; border-radius: 8px; border: 1px solid #edf2f7; width: 100%;">
                    <i class="far fa-file-alt mr-3 text-primary" style="font-size: 1.5rem;"></i>
                    <div class="text-left flex-grow-1" style="overflow: hidden;">
                        <div class="font-weight-bold" style="font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${(file.size / (1024 * 1024)).toFixed(2)} MB</div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" id="remove-file-btn" style="padding: 4px 8px; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                </div>
            `;

            document.getElementById('remove-file-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                this.input.value = '';
                this.preview.innerHTML = '';
            });
        }
    }
}

// Badges generators
class BadgeHelper {
    static getStatus(status) {
        let color = '#95a5a6';
        if (status === 'Pending') color = '#f39c12';
        if (status === 'In Progress') color = '#3498db';
        if (status === 'Resolved') color = '#2ecc71';
        if (status === 'Rejected') color = '#e74c3c';

        return `<span class="badge" style="background-color: ${color}">${status}</span>`;
    }

    static getPriority(priority) {
        let color = '#95a5a6';
        if (priority === 'High') color = '#e74c3c';
        if (priority === 'Medium') color = '#f39c12';
        if (priority === 'Low') color = '#2ecc71';

        return `<span class="badge" style="background-color: ${color}">${priority}</span>`;
    }
}

// Pagination Renderer
class Pagination {
    static render(paginationData, containerId, onPageClick) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';
        if (paginationData.last_page <= 1) return;

        const nav = document.createElement('nav');
        nav.className = 'd-flex justify-content-center mt-4';
        
        const ul = document.createElement('ul');
        ul.className = 'd-flex align-items-center' ;
        ul.style.listStyle = 'none';
        ul.style.gap = '8px';

        // Prev Button
        if (paginationData.current_page > 1) {
            ul.appendChild(this.createPageItem('Prev', paginationData.current_page - 1, onPageClick));
        }

        // Page Numbers
        const startPage = Math.max(1, paginationData.current_page - 2);
        const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            ul.appendChild(this.createPageItem(i, i, onPageClick, i === paginationData.current_page));
        }

        // Next Button
        if (paginationData.current_page < paginationData.last_page) {
            ul.appendChild(this.createPageItem('Next', paginationData.current_page + 1, onPageClick));
        }

        nav.appendChild(ul);
        container.appendChild(nav);
    }

    static createPageItem(label, pageNum, callback, isActive = false) {
        const li = document.createElement('li');
        const btn = document.createElement('button');
        btn.className = isActive ? 'btn btn-primary btn-sm' : 'btn btn-outline-primary btn-sm';
        btn.style.padding = '6px 12px';
        btn.style.fontSize = '0.8rem';
        btn.textContent = label;
        btn.onclick = () => callback(pageNum);
        li.appendChild(btn);
        return li;
    }
}

// TimeAgo relative calculation
function timeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 5) return 'just now';
    if (seconds < 60) return `${seconds} seconds ago`;
    
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    
    const days = Math.floor(hours / 24);
    if (days < 30) return `${days} day${days > 1 ? 's' : ''} ago`;
    
    // Fallback date string
    return date.toLocaleDateString();
}

// Loading Spinner Overlay
class Spinner {
    static show(containerEl) {
        this.hide(containerEl); // prevent multiples
        const spinner = document.createElement('div');
        spinner.className = 'spinner-overlay d-flex justify-content-center align-items-center';
        spinner.style.position = 'absolute';
        spinner.style.top = '0';
        spinner.style.left = '0';
        spinner.style.width = '100%';
        spinner.style.height = '100%';
        spinner.style.background = 'rgba(255,255,255,0.7)';
        spinner.style.zIndex = '100';
        spinner.innerHTML = `
            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
        `;
        
        // Add keyframe stylesheet if not present
        if (!document.getElementById('spin-keyframe-style')) {
            const style = document.createElement('style');
            style.id = 'spin-keyframe-style';
            style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }

        containerEl.style.position = 'relative';
        containerEl.appendChild(spinner);
    }

    static hide(containerEl) {
        const spinner = containerEl.querySelector('.spinner-overlay');
        if (spinner) spinner.remove();
    }
}

// Notification Bell Handler (with 30s Polling)
class NotificationBell {
    constructor(bellId, badgeId, dropdownId, listId) {
        this.bell = document.getElementById(bellId);
        this.badge = document.getElementById(badgeId);
        this.dropdown = document.getElementById(dropdownId);
        this.list = document.getElementById(listId);

        if (!this.bell) return;

        this.init();
    }

    init() {
        this.bell.addEventListener('click', (e) => {
            e.stopPropagation();
            this.dropdown.classList.toggle('show');
            if (this.dropdown.classList.contains('show')) {
                this.loadNotifications();
            }
        });

        // Close dropdown on click outside
        document.addEventListener('click', () => {
            this.dropdown.classList.remove('show');
        });

        // Load initially
        this.loadUnreadCount();

        // 30s polling
        setInterval(() => {
            this.loadUnreadCount();
        }, 30000);
    }

    loadUnreadCount() {
        $.get('/api/notifications/unread-count', (data) => {
            if (data.unread_count > 0) {
                this.badge.textContent = data.unread_count;
                this.badge.classList.remove('d-none');
            } else {
                this.badge.classList.add('d-none');
            }
        });
    }

    loadNotifications() {
        this.list.innerHTML = '<div class="p-3 text-center text-muted"><i class="fas fa-spinner fa-spin"></i></div>';
        
        $.get('/api/notifications', (response) => {
            const notifications = response.notifications.data;
            this.list.innerHTML = '';
            
            if (notifications.length === 0) {
                this.list.innerHTML = '<div class="p-3 text-center text-muted">No notifications.</div>';
                return;
            }

            notifications.forEach(notif => {
                const item = document.createElement('div');
                item.className = `notif-item ${notif.is_read ? '' : 'unread'}`;
                item.innerHTML = `
                    <div style="font-weight: 600; font-size: 0.85rem; color: var(--dark);">${notif.title}</div>
                    <div class="mt-1" style="color: var(--text-light); font-size: 0.78rem;">${notif.message}</div>
                    <div class="mt-1 text-right text-muted" style="font-size: 0.7rem;">${timeAgo(notif.created_at)}</div>
                `;
                
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    $.ajax({
                        url: `/api/notifications/${notif.id}/read`,
                        method: 'PUT',
                        success: () => {
                            this.loadUnreadCount();
                            this.dropdown.classList.remove('show');
                            if (notif.complaint_id) {
                                window.location.href = `/complaints/${notif.complaint_id}`;
                            }
                        }
                    });
                });

                this.list.appendChild(item);
            });
        });
    }
}

// Dark Mode Toggle
class ThemeToggle {
    constructor(buttonId) {
        this.button = document.getElementById(buttonId);
        if (!this.button) return;
        this.init();
    }

    init() {
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            this.updateIcon(true);
        }

        this.button.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            this.updateIcon(isDark);
        });
    }

    updateIcon(isDark) {
        const icon = this.button.querySelector('i');
        if (icon) {
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
}
