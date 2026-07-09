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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $this->redirectByUserRole($user);
        }
        return view('auth.login');
    }

    /**
     * Show the staff login form.
     */
    public function showStaffLoginForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $this->redirectByUserRole($user);
        }
        return view('auth.staff-login');
    }

    /**
     * Show the admin login form.
     */
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $this->redirectByUserRole($user);
        }
        return view('auth.admin-login');
    }

    /**
     * Handle admin authentication.
     */
    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. This portal is reserved for system administrators.'
                ], 403);
            }

            return back()->withErrors([
                'email' => 'Access denied. This portal is reserved for system administrators.',
            ])->withInput($request->only('email'));
        }

        return $this->login($request);
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
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                if ($user->status === 'pending') {
                    Auth::logout();
                    $msg = 'Your account is pending administrator approval.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 403);
                    }
                    return back()->withErrors(['email' => $msg])->withInput($request->only('email'));
                }

                if ($user->status === 'inactive') {
                    Auth::logout();
                    $msg = 'Your account has been deactivated. Please contact support.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 403);
                    }
                    return back()->withErrors(['email' => $msg])->withInput($request->only('email'));
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
                        'redirect' => $this->getRedirectUrl($user),
                        'user' => $user
                    ]);
                }

                return $this->redirectByUserRole($user);
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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $this->redirectByUserRole($user);
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
            'status' => 'pending',
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

        $regMessage = 'Registration successful! Your account is pending administrator approval.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $regMessage,
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login')->with('success', $regMessage);
    }

    /**
     * Show the staff registration form.
     */
    public function showStaffRegistrationForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $this->redirectByUserRole($user);
        }
        return view('auth.staff-register');
    }

    /**
     * Handle staff registration.
     */
    public function staffRegister(RegisterRequest $request)
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
            'role' => 'staff',
            'status' => 'pending', // Awaiting administrator approval
            'avatar' => $avatarPath,
        ]);

        // Send Welcome Email
        try {
            if (setting('enable_email_notifications', '1') === '1') {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to staff {$user->email}: " . $e->getMessage());
        }

        $regMessage = 'Registration successful! Your staff account is pending administrator approval.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $regMessage,
                'redirect' => route('staff.login')
            ]);
        }

        return redirect()->route('staff.login')->with('success', $regMessage);
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

    /**
     * Redirect helper based on user role.
     */
    protected function redirectByUserRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }
        return redirect()->route('dashboard');
    }

    /**
     * Get redirect URL string based on user role.
     */
    protected function getRedirectUrl($user)
    {
        if ($user->role === 'admin') {
            return route('admin.dashboard');
        } elseif ($user->role === 'staff') {
            return route('staff.dashboard');
        }
        return route('dashboard');
    }
}
