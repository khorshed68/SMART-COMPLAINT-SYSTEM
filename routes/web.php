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
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/staff/register', [AuthController::class, 'showStaffRegistrationForm'])->name('staff.register');
    Route::post('/staff/register', [AuthController::class, 'staffRegister']);
    Route::get('/staff/login', [AuthController::class, 'showStaffLoginForm'])->name('staff.login');
    
    // AJAX: Guest email validation
    Route::post('/api/auth/check-email', [AuthController::class, 'checkEmail']);
});

// Authenticated User & Admin Routes (both require active user status and prevent back history caching)
Route::middleware(['auth', 'active', 'prevent-back-history'])->group(function () {
    
    // Auth & Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Web Views
    Route::get('/dashboard', [ComplaintController::class, 'dashboard'])->name('dashboard');
    Route::get('/submit-complaint', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/my-complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/announcements', [AnnouncementController::class, 'userIndex'])->name('announcements.index');
    Route::get('/announcements/{id}', [AnnouncementController::class, 'userShow'])->name('announcements.show');

    // Public Categories List AJAX
    Route::get('/api/categories', [ComplaintController::class, 'getCategories']);

    // User Complaints AJAX
    Route::prefix('api')->group(function () {
        Route::get('/complaints', [ComplaintController::class, 'getComplaints']);
        Route::get('/complaints/{id}', [ComplaintController::class, 'getComplaintDetail']);
        Route::get('/complaints/{id}/updates', [ComplaintController::class, 'getUpdates']);
        Route::get('/stats', [ComplaintController::class, 'getStats']);
        Route::post('/complaints/{id}/rate', [ComplaintController::class, 'rate']);
        
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
        
        // Announcements API
        Route::get('/announcements/active', [AnnouncementController::class, 'active']);
    });

    // Admin Dashboard & Views (Admin Middleware Protected)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminComplaintController::class, 'dashboard'])->name('dashboard');
        Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints');
        Route::get('/complaints/{id}', [AdminComplaintController::class, 'show'])->name('complaints.show');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/staff', [AdminUserController::class, 'staffIndex'])->name('staff');
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::get('/control-panel', [AdminController::class, 'controlPanel'])->name('control-panel');
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
        Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');

        // Announcements Management
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
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

        // Staff Management API
        Route::get('/staff-list', [AdminUserController::class, 'getStaffList']);
        Route::post('/staff', [AdminUserController::class, 'createStaff']);

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
        Route::get('/analytics/satisfaction', [AnalyticsController::class, 'getSatisfactionStats']);
        Route::get('/analytics/export', [AnalyticsController::class, 'export']);

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'getSettings']);
        Route::put('/settings', [AdminSettingController::class, 'updateSettings']);

        // Audit Logs & System Health
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/system-health', [AdminController::class, 'getSystemHealth']);
    });

    // Staff Dashboard & Operations (Staff Middleware Protected)
    Route::middleware('staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/complaints/{id}', [StaffController::class, 'show'])->name('complaints.show');
    });

    // Staff AJAX API Endpoints
    Route::middleware('staff')->prefix('api/staff')->group(function () {
        Route::get('/complaints', [StaffController::class, 'getComplaints']);
        Route::post('/complaints/{id}/status', [StaffController::class, 'updateStatus']);
        Route::post('/complaints/{id}/comment', [StaffController::class, 'addComment']);
    });
});
