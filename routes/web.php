<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AdminComplaintController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // AJAX: Guest email validation
    Route::post('/api/auth/check-email', [AuthController::class, 'checkEmail']);
});

// Authenticated User & Admin Routes (both require active user status)
Route::middleware(['auth', 'active'])->group(function () {
    
    // Auth & Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Web Views
    Route::get('/dashboard', [ComplaintController::class, 'dashboard'])->name('dashboard');
    Route::get('/submit-complaint', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/my-complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    // Public Categories List AJAX
    Route::get('/api/categories', [ComplaintController::class, 'getCategories']);

    // User Complaints AJAX
    Route::prefix('api')->group(function () {
        Route::get('/complaints', [ComplaintController::class, 'getComplaints']);
        Route::get('/complaints/{id}', [ComplaintController::class, 'getComplaintDetail']);
        Route::get('/complaints/{id}/updates', [ComplaintController::class, 'getUpdates']);
        Route::get('/stats', [ComplaintController::class, 'getStats']);
        
        // Profile AJAX
        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'changePassword']);
        Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
        
        // Notifications AJAX
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/notifications', [NotificationController::class, 'destroyAll']);
    });

    // Admin Dashboard & Views (Admin Middleware Protected)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminComplaintController::class, 'dashboard'])->name('dashboard');
        Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints');
        Route::get('/complaints/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::get('/control-panel', [AdminController::class, 'controlPanel'])->name('control-panel');
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
        Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');
    });

    // Admin API AJAX Endpoints
    Route::middleware('admin')->prefix('api/admin')->group(function () {
        // Complaint Admin Operations
        Route::post('/complaints/{id}/status', [AdminComplaintController::class, 'updateStatus']);
        Route::post('/complaints/{id}/assign', [AdminComplaintController::class, 'assign']);
        Route::post('/complaints/{id}/priority', [AdminComplaintController::class, 'changePriority']);
        Route::post('/complaints/{id}/comment', [AdminComplaintController::class, 'addComment']);
        Route::delete('/complaints/{id}', [AdminComplaintController::class, 'destroy']);

        // User Management
        Route::get('/users', [AdminUserController::class, 'getUsers']);
        Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
        Route::put('/users/{id}/role', [AdminUserController::class, 'updateRole']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

        // Category Operations
        Route::get('/categories', [AdminCategoryController::class, 'getCategories']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

        // Analytics Data
        Route::get('/analytics/overview', [AnalyticsController::class, 'getOverview']);
        Route::get('/analytics/trends', [AnalyticsController::class, 'getTrends']);
        Route::get('/analytics/categories', [AnalyticsController::class, 'getCategoryStats']);
        Route::get('/analytics/resolution', [AnalyticsController::class, 'getResolutionStats']);
        Route::get('/analytics/distribution', [AnalyticsController::class, 'getDistribution']);
        Route::get('/analytics/export', [AnalyticsController::class, 'export']);

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'getSettings']);
        Route::put('/settings', [AdminSettingController::class, 'updateSettings']);

        // Audit Logs & System Health
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/system-health', [AdminController::class, 'getSystemHealth']);
    });
});
