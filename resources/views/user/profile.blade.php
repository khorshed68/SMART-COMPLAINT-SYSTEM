@extends('layouts.app')

@section('title', 'Profile Settings - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="container fade-in" style="max-width: 800px;">
    <h1 class="dashboard-section-title mb-4"><i class="fas fa-user-cog"></i> Profile Settings</h1>
    
    <div class="row">
        <!-- Avatar card -->
        <div class="col-md-4">
            <div class="card mb-4 text-center">
                <div class="card-header">Profile Avatar</div>
                <div class="card-body py-4">
                    <img id="profile-avatar-img" src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: var(--shadow); margin-bottom: 20px;">
                    
                    <form id="avatar-form" onsubmit="uploadAvatar(event)" enctype="multipart/form-data">
                        <input type="file" id="avatar-input" name="avatar" style="display: none;" onchange="document.getElementById('avatar-submit-btn').click();" accept="image/*">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="document.getElementById('avatar-input').click();">Choose Image</button>
                        <button type="submit" id="avatar-submit-btn" style="display: none;"></button>
                    </form>
                    <p class="text-muted" style="font-size: 0.75rem;">Max file size: 2MB (jpg, jpeg, png, gif)</p>
                </div>
            </div>
        </div>

        <!-- Details card -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Contact Information</div>
                <div class="card-body">
                    <form id="profile-details-form" onsubmit="updateProfile(event)">
                        <div class="form-group">
                            <label for="prof-name">Full Name</label>
                            <input type="text" id="prof-name" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="prof-email">Email Address</label>
                            <input type="email" id="prof-email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group">
                                    <label for="prof-phone">Phone Number</label>
                                    <input type="text" id="prof-phone" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group">
                                    <label for="prof-dept">Department</label>
                                    <input type="text" id="prof-dept" name="department" class="form-control" value="{{ Auth::user()->department }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Save Details</button>
                    </form>
                </div>
            </div>

            <!-- Password Card -->
            <div class="card">
                <div class="card-header">Security Credentials</div>
                <div class="card-body">
                    <form id="profile-password-form" onsubmit="changePassword(event)">
                        <div class="form-group">
                            <label for="pass-current">Current Password</label>
                            <input type="password" id="pass-current" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group">
                                    <label for="pass-new">New Password</label>
                                    <input type="password" id="pass-new" name="new_password" class="form-control" required placeholder="••••••••" onkeyup="checkPasswordStrength(this.value)">
                                    <div class="strength-meter">
                                        <div id="strength-bar" class="strength-bar"></div>
                                    </div>
                                    <div id="strength-text" class="strength-text text-muted">Weak</div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group">
                                    <label for="pass-confirm">Confirm New Password</label>
                                    <input type="password" id="pass-confirm" name="new_password_confirmation" class="form-control" required placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark mt-2">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateProfile(event) {
        event.preventDefault();
        const form = event.target;
        const formData = $(form).serialize();
        const submitBtn = $(form).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: '/api/profile',
            method: 'PUT',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).text('Save Details');
                Toast.show(response.message, 'success');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Save Details');
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    Toast.show(Object.values(errors)[0][0], 'error');
                } else {
                    Toast.show('Failed to save profile.', 'error');
                }
            }
        });
    }

    function changePassword(event) {
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

    function uploadAvatar(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        Toast.show('Uploading avatar...', 'info');

        $.ajax({
            url: '/api/profile/avatar',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                document.getElementById('profile-avatar-img').src = response.avatar_url;
                Toast.show(response.message, 'success');
                // Refresh main header avatar
                $('.user-avatar').attr('src', response.avatar_url);
            },
            error: function(xhr) {
                Toast.show(xhr.responseJSON.message || 'Upload failed.', 'error');
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
