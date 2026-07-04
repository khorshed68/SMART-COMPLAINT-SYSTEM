@extends('layouts.app')

@section('title', 'Log In - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="auth-split-container fade-in">
    <div class="auth-split-card slide-up">
        <!-- Left Banner Panel -->
        <div class="auth-split-visual">
            <div class="auth-visual-header">
                <span class="auth-logo">SCS</span>
                <a href="/" class="auth-back-link">Back to website &rarr;</a>
            </div>
            
            <div class="auth-visual-footer">
                <h3 class="auth-visual-slogan">
                    Resolving Issues,<br>
                    Building Trust.
                </h3>
                <div class="auth-slider-dots">
                    <span class="auth-dot active"></span>
                    <span class="auth-dot"></span>
                    <span class="auth-dot"></span>
                </div>
            </div>
        </div>
        
        <!-- Right Form Panel -->
        <div class="auth-split-form">
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">
                Please log in to manage your complaints.
            </p>

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form onsubmit="submitLogin(event)" autocomplete="off">
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                </div>
                
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="login-password" style="margin-bottom: 0;">Password</label>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="login-password" name="password" class="form-control" placeholder="••••••••" required style="padding-right: 45px;">
                        <button type="button" onclick="const p = document.getElementById('login-password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye-slash');" class="btn" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 5px 10px; color: #64748b; cursor: pointer;">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4 py-3">Log In</button>
            </form>

            <div class="text-left mt-4" style="font-size: 0.9rem;">
                <span class="text-muted">Don't have an account?</span>
                <a href="{{ route('register') }}" class="font-weight-bold" style="color: #705ecf; text-decoration: none;">Register Now</a>
            </div>
        </div>
    </div>
</div>
@endsection
