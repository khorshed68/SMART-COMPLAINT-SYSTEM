@extends('layouts.app')

@section('title', 'Staff Registration - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="auth-split-container fade-in">
    <div class="auth-split-card slide-up" style="max-width: 1050px; min-height: 680px;">
        <!-- Left Banner Panel -->
        <div class="auth-split-visual" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.45) 0%, rgba(49, 46, 129, 0.45) 100%), url('{{ asset('images/login_banner.png') }}'); background-size: cover; background-position: center;">
            <div class="auth-visual-header">
                <span class="auth-logo">SCS</span>
                <a href="/" class="auth-back-link">Back to website &rarr;</a>
            </div>
            
            <div class="auth-visual-footer">
                <h3 class="auth-visual-slogan">
                    Join Our Crew,<br>
                    Resolve Campus Issues.
                </h3>
                <div class="auth-slider-dots">
                    <span class="auth-dot"></span>
                    <span class="auth-dot active"></span>
                    <span class="auth-dot"></span>
                </div>
            </div>
        </div>
        
        <!-- Right Form Panel -->
        <div class="auth-split-form" style="padding: 35px 45px; position: relative;">
            
            <h2 class="auth-title" style="font-size: 2rem; margin-bottom: 4px;">Create Staff Account</h2>
            <p class="auth-subtitle" style="margin-bottom: 20px;">
                Already have a staff account? <a href="{{ route('staff.login') }}" style="color: #705ecf; text-decoration: none; font-weight: 600;">Log in</a>
            </p>

            <form action="/staff/register" onsubmit="submitRegister(event)" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-name">Full Name</label>
                            <input type="text" id="reg-name" name="name" class="form-control" placeholder="Sheikh Khorshed" required>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-phone">Phone Number</label>
                            <input type="text" id="reg-phone" name="phone" class="form-control" placeholder="017XXXXXXXX" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-email">Staff Email Address</label>
                    <input type="email" id="reg-email" name="email" class="form-control" placeholder="plumber@complaint.system" required onkeyup="checkEmailAvailability(this.value, 'email-feedback')" readonly onfocus="this.removeAttribute('readonly');">
                    <div id="email-feedback" class="mt-2" style="font-size: 0.8rem;"></div>
                </div>

                <div class="row">
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-dept">Assign Department</label>
                            <select id="reg-dept" name="department" class="form-select" required>
                                <option value="">Select Specialty / Department</option>
                                <option value="Maintenance & Plumbing">Maintenance & Plumbing</option>
                                <option value="Electrical Department">Electrical Department</option>
                                <option value="IT & Network Services">IT & Network Services</option>
                                <option value="Carpentry & Infrastructure">Carpentry & Infrastructure</option>
                                <option value="Housekeeping & Cleaning">Housekeeping & Cleaning</option>
                                <option value="Administration & Security">Administration & Security</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-avatar">Profile Picture (Optional)</label>
                            <input type="file" id="reg-avatar" name="avatar" class="form-control" accept="image/*" style="padding: 7px 12px !important;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-password">Password</label>
                            <input type="password" id="reg-password" name="password" class="form-control" placeholder="••••••••" required onkeyup="checkPasswordStrength(val => {})" readonly onfocus="this.removeAttribute('readonly');">
                            <div class="strength-meter">
                                <div id="strength-bar" class="strength-bar"></div>
                            </div>
                            <div id="strength-text" class="strength-text text-muted">Weak</div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0 8px;">
                        <div class="form-group">
                            <label for="reg-confirm">Confirm Password</label>
                            <input type="password" id="reg-confirm" name="password_confirmation" class="form-control" placeholder="••••••••" required readonly onfocus="this.removeAttribute('readonly');">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3 py-3">Create Staff Account</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Real-time password strength meter helper
    $('#reg-password').on('keyup', function() {
        const val = this.value;
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
    });

    $(document).ready(function() {
        // Immediate profile picture size check
        $('#reg-avatar').change(function() {
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    $(this).val('');
                    Toast.show('The selected picture size is too large. Please upload an image under 2MB.', 'warning');
                }
            }
        });
    });
</script>
@endsection
