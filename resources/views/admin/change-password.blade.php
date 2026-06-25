@extends('layouts.admin')

@section('title', 'Change Password - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="fade-in" style="max-width: 600px; margin: 0 auto;">
    <h1 class="dashboard-section-title mb-4"><i class="fas fa-key"></i> Change Admin Password</h1>

    <div class="card">
        <div class="card-body">
            <form id="admin-password-form" onsubmit="changeAdminPassword(event)">
                <div class="form-group">
                    <label for="pass-current">Current Password</label>
                    <input type="password" id="pass-current" name="current_password" class="form-control" required placeholder="••••••••">
                </div>
                
                <div class="form-group">
                    <label for="pass-new">New Password</label>
                    <input type="password" id="pass-new" name="new_password" class="form-control" required placeholder="••••••••" onkeyup="checkPasswordStrength(this.value)">
                    <div class="strength-meter">
                        <div id="strength-bar" class="strength-bar"></div>
                    </div>
                    <div id="strength-text" class="strength-text text-muted">Weak</div>
                </div>

                <div class="form-group mb-4">
                    <label for="pass-confirm">Confirm New Password</label>
                    <input type="password" id="pass-confirm" name="new_password_confirmation" class="form-control" required placeholder="••••••••">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeAdminPassword(event) {
        event.preventDefault();
        const form = event.target;
        const formData = $(form).serialize();
        const submitBtn = $(form).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: '/api/profile/password',
            method: 'PUT',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).text('Update Password');
                form.reset();
                checkPasswordStrength('');
                Toast.show(response.message, 'success');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Update Password');
                Toast.show(xhr.responseJSON.message || 'Action failed.', 'error');
            }
        });
    }

    function checkPasswordStrength(val) {
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        
        let score = 0;
        if (!val) {
            bar.style.width = '0';
            text.textContent = '';
            return;
        }

        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        let percentage = (score / 5) * 100;
        bar.style.width = percentage + '%';

        if (score <= 2) {
            bar.style.backgroundColor = 'var(--danger)';
            text.textContent = 'Weak';
            text.style.color = 'var(--danger)';
        } else if (score <= 4) {
            bar.style.backgroundColor = 'var(--warning)';
            text.textContent = 'Medium';
            text.style.color = 'var(--warning)';
        } else {
            bar.style.backgroundColor = 'var(--secondary)';
            text.textContent = 'Strong';
            text.style.color = 'var(--secondary)';
        }
    }
</script>
@endsection
