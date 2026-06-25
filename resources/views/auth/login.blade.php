@extends('layouts.app')

@section('title', 'Log In - ' . setting('site_name', 'Smart Complaint System'))

@section('content')
<div class="auth-wrapper fade-in">
    <div class="auth-card slide-up">
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Please log in to manage your complaints</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
                    <button type="button" onclick="const p = document.getElementById('login-password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye-slash');" class="btn" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 5px 10px; color: var(--text-light); cursor: pointer;">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-4 py-3" style="font-weight: 600; letter-spacing: 0.5px;">Log In</button>
        </form>

        <div class="text-center mt-4" style="font-size: 0.9rem;">
            <span class="text-muted">Don't have an account?</span>
            <a href="{{ route('register') }}" class="font-weight-bold" style="color: var(--primary); text-decoration: none;">Register Now</a>
        </div>
    </div>
</div>
@endsection
