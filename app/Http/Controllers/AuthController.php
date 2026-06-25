<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle authentication.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Check if account is locked
            if ($user->locked_until && $user->locked_until->isFuture()) {
                $diff = $user->locked_until->diffInMinutes(now()) + 1;
                return back()->withErrors([
                    'email' => "Your account is temporarily locked. Please try again in {$diff} minutes.",
                ])->withInput($request->only('email'));
            }

            if (Auth::attempt($credentials)) {
                // Authentication passed
                $user = Auth::user();
                
                if (!$user->isActive()) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your account is deactivated. Please contact support.',
                    ])->withInput($request->only('email'));
                }

                // Reset login attempts
                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => null,
                    'last_login' => now(),
                ]);

                $request->session()->regenerate();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $user->isAdmin() ? route('admin.dashboard') : route('dashboard'),
                        'user' => $user
                    ]);
                }

                return $user->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('dashboard');
            }

            // Increment login attempts
            $attempts = $user->login_attempts + 1;
            $lockedUntil = null;
            if ($attempts >= 5) {
                $lockedUntil = now()->addMinutes(15);
                $attempts = 0; // reset attempts after locking
            }

            $user->update([
                'login_attempts' => $attempts,
                'locked_until' => $lockedUntil,
            ]);

            $errorMessage = $lockedUntil 
                ? 'Too many login attempts. Your account is locked for 15 minutes.'
                : 'Invalid email or password.';
        } else {
            $errorMessage = 'Invalid email or password.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 422);
        }

        return back()->withErrors([
            'email' => $errorMessage,
        ])->withInput($request->only('email'));
    }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request)
    {
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'department' => $request->department,
            'role' => 'user',
            'status' => 'active',
            'avatar' => $avatarPath,
        ]);

        // Send Welcome Email
        try {
            if (setting('enable_email_notifications', '1') === '1') {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please login.',
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login')->with('success', 'Registration successful! You can now log in.');
    }

    /**
     * Check if email exists (AJAX validation).
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Log out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login');
    }
}
