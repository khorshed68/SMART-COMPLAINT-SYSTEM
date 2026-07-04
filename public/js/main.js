/* Main Application JavaScript - jQuery Powered */

// AJAX Setup with CSRF Token
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Animate Counter
function animateCounter(elementId, targetValue, duration = 1000) {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    let start = 0;
    const end = parseInt(targetValue, 10);
    if (start === end) {
        el.textContent = end;
        return;
    }
    
    const range = end - start;
    let current = start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    
    const timer = setInterval(() => {
        current += increment;
        el.textContent = current;
        if (current == end) {
            clearInterval(timer);
        }
    }, Math.max(stepTime, 10));
}

// Format Date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const d = new Date(dateString);
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// Load Categories into Select element
function loadCategories(selectId, selectedId = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = '<option value="">Loading categories...</option>';
    
    $.get('/api/categories', function(data) {
        select.innerHTML = '<option value="">Select a Category</option>';
        data.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            if (selectedId && cat.id == selectedId) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
    });
}

// User Dashboard Counter Loader
function loadDashboardStats() {
    $.get('/api/stats', function(stats) {
        animateCounter('stat-total', stats.total);
        animateCounter('stat-pending', stats.pending);
        animateCounter('stat-in_progress', stats.in_progress);
        animateCounter('stat-resolved', stats.resolved);
    });
}

// Submit Login Action via AJAX
function submitLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = $(form).serialize();
    const submitBtn = $(form).find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Logging in...');

    $.ajax({
        url: '/login',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Toast.show('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1000);
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).text('Log In');
            const response = xhr.responseJSON;
            const msg = response && response.message ? response.message : 'Invalid credentials.';
            Toast.show(msg, 'error');
        }
    });
}

// Submit Registration via AJAX
function submitRegister(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = $(form).find('button[type="submit"]');

    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating account...');

    $.ajax({
        url: '/register',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Toast.show(response.message, 'success');
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).text('Register');
            const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
            if (errors) {
                // Focus on first validation error message
                const keys = Object.keys(errors);
                Toast.show(errors[keys[0]][0], 'error');
            } else {
                Toast.show('Registration failed. Please check inputs.', 'error');
            }
        }
    });
}

// Check Email Availability Debounced
const checkEmailAvailability = debounce(function(email, feedbackElId) {
    const feedback = document.getElementById(feedbackElId);
    if (!feedback || !email) return;

    feedback.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> Checking availability...</span>';

    $.post('/api/auth/check-email', { email: email }, function(data) {
        if (data.exists) {
            feedback.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Email is already registered.</span>';
        } else {
            feedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Email is available.</span>';
        }
    });
}, 500);

// Submit Complaint via FormData
function submitComplaint(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = $(form).find('button[type="submit"]');

    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting complaint...');

    $.ajax({
        url: '/complaints',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Toast.show('Complaint submitted successfully!', 'success');
            setTimeout(() => {
                window.location.href = response.redirect;
            }, 1000);
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).text('Submit Complaint');
            const response = xhr.responseJSON;
            if (response && response.errors) {
                const keys = Object.keys(response.errors);
                Toast.show(response.errors[keys[0]][0], 'error');
            } else {
                Toast.show('Failed to submit complaint.', 'error');
            }
        }
    });
}

// Load Paginated Complaints (for User Listings)
let currentFilters = {};
function loadComplaints(page = 1, filters = {}) {
    const container = document.getElementById('complaints-list-container');
    if (!container) return;

    Spinner.show(container);
    currentFilters = filters;
    filters.page = page;

    $.get('/api/complaints', filters, function(response) {
        Spinner.hide(container);
        container.innerHTML = '';
        
        const complaints = response.data;
        if (complaints.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center p-5 bg-white" style="border-radius: 12px; border: 1.5px solid #edf2f7;">
                    <i class="far fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted">No complaints found matching the criteria.</p>
                </div>
            `;
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'complaint-grid w-100';

        complaints.forEach(item => {
            const card = document.createElement('a');
            card.href = `/complaints/${item.id}`;
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

        // Draw pagination
        Pagination.render(response, 'complaints-pagination-container', (pageNum) => {
            loadComplaints(pageNum, currentFilters);
        });
    });
}

// Interactive Star Rating Selectors & Form Post handler
$(document).ready(function() {
    // Star hover & click interactions
    const ratingLabels = {
        0: 'Select a rating',
        1: 'Poor - Very dissatisfied',
        2: 'Fair - Unsatisfied',
        3: 'Good - Neutral/Average',
        4: 'Very Good - Satisfied',
        5: 'Excellent - Very satisfied'
    };

    $(document).on('mouseenter', '.rating-stars-select .star-btn', function() {
        const starNum = parseInt($(this).data('star'), 10);
        const parent = $(this).closest('.rating-stars-select');
        
        parent.find('.star-btn').each(function() {
            const currentNum = parseInt($(this).data('star'), 10);
            const icon = $(this).find('i');
            if (currentNum <= starNum) {
                $(this).addClass('hovered');
                icon.removeClass('far').addClass('fas');
            } else {
                $(this).removeClass('hovered');
                icon.removeClass('fas').addClass('far');
            }
        });
        
        $('#rating-label').text(ratingLabels[starNum]).removeClass('text-muted').addClass('text-warning');
    });

    $(document).on('mouseleave', '.rating-stars-select', function() {
        const parent = $(this);
        const selectedValue = parseInt($('#rating-input-value').val(), 10);
        
        parent.find('.star-btn').each(function() {
            const currentNum = parseInt($(this).data('star'), 10);
            const icon = $(this).find('i');
            $(this).removeClass('hovered');
            if (currentNum <= selectedValue) {
                $(this).addClass('selected');
                icon.removeClass('far').addClass('fas');
            } else {
                $(this).removeClass('selected');
                icon.removeClass('fas').addClass('far');
            }
        });
        
        if (selectedValue > 0) {
            $('#rating-label').text(ratingLabels[selectedValue]).removeClass('text-muted').addClass('text-warning');
        } else {
            $('#rating-label').text(ratingLabels[0]).removeClass('text-warning').addClass('text-muted');
        }
    });

    $(document).on('click', '.rating-stars-select .star-btn', function() {
        const starNum = parseInt($(this).data('star'), 10);
        $('#rating-input-value').val(starNum);
        
        $(this).closest('.rating-stars-select').find('.star-btn').each(function() {
            const currentNum = parseInt($(this).data('star'), 10);
            if (currentNum <= starNum) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
        
        $('#submit-feedback-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    });

    // Rating Form Submit Handler
    $(document).on('submit', '#rating-feedback-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#submit-feedback-btn');
        const rating = $('#rating-input-value').val();
        const feedback = $('#feedback-text').val();
        
        // Extract complaint ID from URL
        const urlParts = window.location.pathname.split('/');
        const complaintId = urlParts[urlParts.length - 1];
        
        if (!complaintId || isNaN(complaintId)) {
            Toast.show('Error identifying complaint ID.', 'error');
            return;
        }

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: `/api/complaints/${complaintId}/rate`,
            method: 'POST',
            data: {
                rating: rating,
                feedback: feedback
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Toast.show(response.message, 'success');
                    
                    // Re-render card with static rating display
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= response.rating) {
                            starsHtml += '<i class="fas fa-star text-warning fa-2x"></i>';
                        } else {
                            starsHtml += '<i class="far fa-star text-warning fa-2x"></i>';
                        }
                    }
                    
                    const container = $('#satisfaction-rating-card .card-body');
                    container.html(`
                        <div class="text-center py-2 fade-in">
                            <div class="mb-2">
                                ${starsHtml}
                            </div>
                            <h4 class="font-weight-bold mb-3" style="color: var(--dark);">
                                Rating: ${response.rating} / 5
                            </h4>
                            ${response.feedback ? `
                                <blockquote class="p-3 bg-light border italic" style="border-radius: 8px; font-size: 0.92rem; color: #4a5568; margin: 15px auto; max-width: 600px; border-left: 4px solid var(--primary) !important; text-align: left;">
                                    "${response.feedback}"
                                </blockquote>
                            ` : '<p class="text-muted italic mb-0" style="font-size: 0.9rem;">No comments provided.</p>'}
                            <div class="text-muted mt-3" style="font-size: 0.75rem;">
                                Rated on Just now
                            </div>
                        </div>
                    `);
                    
                    // Refresh the timeline
                    if (typeof loadTimeline === 'function') {
                        loadTimeline();
                    }
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Submit Feedback');
                const response = xhr.responseJSON;
                const msg = response && response.message ? response.message : 'Error submitting feedback.';
                Toast.show(msg, 'error');
            }
        });
    });
});
