@extends('layouts.app')

@section('title', 'Register - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="auth-wrapper fade-in">
    <div class="auth-card slide-up" style="max-width: 550px;">
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Join us to start submitting and tracking complaints</p>

        <form onsubmit="submitRegister(event)" autocomplete="off" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6" style="padding: 0 10px;">
                    <div class="form-group">
                        <label for="reg-name">Full Name</label>
                        <input type="text" id="reg-name" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0 10px;">
                    <div class="form-group">
                        <label for="reg-phone">Phone Number</label>
                        <input type="text" id="reg-phone" name="phone" class="form-control" placeholder="+123456789">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="reg-email">Email Address</label>
                <input type="email" id="reg-email" name="email" class="form-control" placeholder="john@example.com" required onkeyup="checkEmailAvailability(this.value, 'email-feedback')">
                <div id="email-feedback" class="mt-2" style="font-size: 0.8rem;"></div>
            </div>

            <div class="form-group">
                <label for="reg-dept">Department</label>
                <input type="text" id="reg-dept" name="department" class="form-control" placeholder="e.g. Computer Science">
            </div>

            <div class="form-group">
                <label for="reg-avatar">Profile Picture (Optional)</label>
                <input type="file" id="reg-avatar" name="avatar" class="form-control" accept="image/*">
            </div>

            <div class="row">
                <div class="col-md-6" style="padding: 0 10px;">
                    <div class="form-group">
                        <label for="reg-password">Password</label>
                        <input type="password" id="reg-password" name="password" class="form-control" placeholder="••••••••" required onkeyup="checkPasswordStrength(this.value)">
                        <div class="strength-meter">
                            <div id="strength-bar" class="strength-bar"></div>
                        </div>
                        <div id="strength-text" class="strength-text text-muted">Weak</div>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0 10px;">
                    <div class="form-group">
                        <label for="reg-confirm">Confirm Password</label>
                        <input type="password" id="reg-confirm" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-4 py-3" style="font-weight: 600;">Create Account</button>
        </form>

        <div class="text-center mt-4" style="font-size: 0.9rem;">
            <span class="text-muted">Already have an account?</span>
            <a href="{{ route('login') }}" class="font-weight-bold" style="color: var(--primary); text-decoration: none;">Log In</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
