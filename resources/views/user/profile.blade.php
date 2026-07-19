@extends('layouts.app')

@section('title', 'Profile Settings - ' . setting('site_name', 'Smart Complaint System'))

@section('styles')
<style>
    .profile-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
        background: #ffffff !important;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(112, 94, 207, 0.08) !important;
    }
    .profile-card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
        padding: 20px 24px !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        color: var(--dark) !important;
    }
    .profile-card-body {
        padding: 28px 24px !important;
    }
    .profile-avatar-container {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 20px;
    }
    .profile-avatar-hover {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(112, 94, 207, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
        color: #ffffff;
        font-size: 1.25rem;
    }
    .profile-avatar-container:hover .profile-avatar-hover {
        opacity: 1;
    }
    .profile-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transition: border-color 0.3s ease;
    }
    .profile-avatar-container:hover .profile-avatar-img {
        border-color: var(--primary);
    }
    .profile-label {
        font-size: 0.76rem !important;
        font-weight: 700 !important;
        color: #64748b !important;
        margin-bottom: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.8px !important;
        display: inline-block;
    }
    .profile-input {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
        background-color: #f8fafc !important;
        color: var(--dark) !important;
        transition: all 0.2s ease-in-out !important;
    }
    .profile-input:focus {
        background-color: #ffffff !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.15) !important;
        outline: none !important;
    }
    .strength-meter {
        height: 5px;
        background-color: #e2e8f0;
        border-radius: 3px;
        margin-top: 8px;
        overflow: hidden;
    }
    .strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    .strength-text {
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 600;
    }
    .btn-save {
        border-radius: 10px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(52, 152, 219, 0.35) !important;
    }
    .btn-update-password {
        border-radius: 10px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        background-color: var(--dark) !important;
        border-color: var(--dark) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2) !important;
        transition: all 0.2s ease !important;
    }
    .btn-update-password:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(44, 62, 80, 0.3) !important;
        background-color: #1a252f !important;
        border-color: #1a252f !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid fade-in-up" style="margin-top: 20px; margin-bottom: 50px; padding: 0 15px;">
    <div class="dashboard-section-header mb-4">
        <h1 class="dashboard-section-title mb-0"><i class="fas fa-user-cog text-primary mr-2"></i> Profile Settings</h1>
    </div>
    
    <div class="row">
        <!-- Column 1: Profile Avatar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card profile-card text-center shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header profile-card-header">Profile Avatar</div>
                <div class="card-body profile-card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="profile-avatar-container" onclick="document.getElementById('avatar-input').click();">
                        <img id="profile-avatar-img" src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(Auth::user()->email))) . '?d=mp' }}" alt="Avatar" class="profile-avatar-img">
                        <div class="profile-avatar-hover">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    
                    <form id="avatar-form" onsubmit="uploadAvatar(event)" enctype="multipart/form-data">
                        <input type="file" id="avatar-input" name="avatar" style="display: none;" onchange="document.getElementById('avatar-submit-btn').click();" accept="image/*">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2 font-weight-bold" style="border-radius: 8px;" onclick="document.getElementById('avatar-input').click();">Choose Image</button>
                        <button type="submit" id="avatar-submit-btn" style="display: none;"></button>
                    </form>
                    <p class="text-muted mb-0" style="font-size: 0.72rem; line-height: 1.4;">Max file size: 2MB<br>(jpg, jpeg, png, gif)</p>
                </div>
            </div>
        </div>

        <!-- Column 2: Contact Information -->
        <div class="col-lg-5 col-md-8 mb-4">
            <div class="card profile-card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header profile-card-header">Contact Information</div>
                <div class="card-body profile-card-body">
                    <form id="profile-details-form" onsubmit="updateProfile(event)">
                        <div class="form-group mb-3">
                            <label class="profile-label" for="prof-name">Full Name</label>
                            <input type="text" id="prof-name" name="name" class="form-control profile-input" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="profile-label" for="prof-email">Email Address</label>
                            <input type="email" id="prof-email" name="email" class="form-control profile-input" value="{{ Auth::user()->email }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group mb-3">
                                    <label class="profile-label" for="prof-phone">Phone Number</label>
                                    <input type="text" id="prof-phone" name="phone" class="form-control profile-input" value="{{ Auth::user()->phone }}">
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0 10px;">
                                <div class="form-group mb-3">
                                    <label class="profile-label" for="prof-dept">Department</label>
                                    <input type="text" id="prof-dept" name="department" class="form-control profile-input" value="{{ Auth::user()->department }}">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-save w-100"><i class="fas fa-save mr-2"></i> Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Column 3: Security Credentials -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card profile-card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header profile-card-header">Security Credentials</div>
                <div class="card-body profile-card-body">
                    <form id="profile-password-form" onsubmit="changePassword(event)">
                        <div class="form-group mb-3">
                            <label class="profile-label" for="pass-current">Current Password</label>
                            <input type="password" id="pass-current" name="current_password" class="form-control profile-input" required placeholder="••••••••">
                        </div>
                        <div class="form-group mb-3">
                            <label class="profile-label" for="pass-new">New Password</label>
                            <input type="password" id="pass-new" name="new_password" class="form-control profile-input" required placeholder="••••••••" onkeyup="checkPasswordStrength(this.value)">
                            <div class="strength-meter">
                                <div id="strength-bar" class="strength-bar"></div>
                            </div>
                            <div id="strength-text" class="strength-text text-muted">Weak</div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="profile-label" for="pass-confirm">Confirm New Password</label>
                            <input type="password" id="pass-confirm" name="new_password_confirmation" class="form-control profile-input" required placeholder="••••••••">
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-update-password w-100"><i class="fas fa-key mr-2"></i> Update Password</button>
                        </div>
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

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

        $.ajax({
            url: '/api/profile',
            method: 'PUT',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Save Details');
                Toast.show(response.message, 'success');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Save Details');
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

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Updating...');

        $.ajax({
            url: '/api/profile/password',
            method: 'PUT',
            data: formData,
            success: function(response) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-key mr-2"></i> Update Password');
                form.reset();
                checkPasswordStrength('');
                Toast.show(response.message, 'success');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-key mr-2"></i> Update Password');
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
