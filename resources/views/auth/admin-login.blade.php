@extends('layouts.app')

@section('title', 'Admin Login - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="auth-split-container fade-in">
    <div class="auth-split-card slide-up">
        <!-- Left Banner Panel -->
        <div class="auth-split-visual" style="background-image: linear-gradient(135deg, rgba(30, 27, 75, 0.45) 0%, rgba(49, 16, 66, 0.45) 100%), url('{{ asset('images/login_banner.png') }}'); background-size: cover; background-position: center;">
            <div class="auth-visual-header">
                <span class="auth-logo">SCS</span>
                <a href="/" class="auth-back-link">Back to website &rarr;</a>
            </div>
            
            <div class="auth-visual-footer">
                <h3 class="auth-visual-slogan">
                    Admin Console.<br>
                    Managing Issues.
                </h3>
                <div class="auth-slider-dots">
                    <span class="auth-dot active"></span>
                    <span class="auth-dot"></span>
                    <span class="auth-dot"></span>
                </div>
            </div>
        </div>
        
        <!-- Right Form Panel -->
        <div class="auth-split-form" style="position: relative;">
            <div style="position: absolute; right: 25px; top: 25px; display: flex; gap: 8px;">
                <a href="{{ route('login') }}" class="btn btn-outline-primary" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600; padding: 6px 14px; border: 1px solid rgba(112, 94, 207, 0.4); color: #bca8ff; background: rgba(112, 94, 207, 0.05); text-decoration: none; transition: all 0.2s ease;">
                    <i class="fas fa-user mr-1"></i> Student Login
                </a>
                <a href="{{ route('staff.login') }}" class="btn btn-outline-primary" style="border-radius: 20px; font-size: 0.78rem; font-weight: 600; padding: 6px 14px; border: 1px solid rgba(112, 94, 207, 0.4); color: #bca8ff; background: rgba(112, 94, 207, 0.05); text-decoration: none; transition: all 0.2s ease;">
                    <i class="fas fa-tools mr-1"></i> Staff Login
                </a>
            </div>
            
            <h2 class="auth-title">Admin Console</h2>
            <p class="auth-subtitle">
                Please log in to control the admin portal.
            </p>

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.login') }}" onsubmit="submitLogin(event)" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label for="login-email">Admin Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="admin@example.com" required autofocus readonly onfocus="this.removeAttribute('readonly');">
                </div>
                
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="login-password" style="margin-bottom: 0;">Password</label>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="login-password" name="password" class="form-control" placeholder="••••••••" required style="padding-right: 45px;" readonly onfocus="this.removeAttribute('readonly');">
                        <button type="button" onclick="const p = document.getElementById('login-password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye-slash');" class="btn" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 5px 10px; color: #64748b; cursor: pointer;">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary py-3" style="width: 200px; font-weight: 600; border-radius: 8px;">Log In</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
