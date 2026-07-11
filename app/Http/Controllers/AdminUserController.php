<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\AccountApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    /**
     * Users management index view.
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * AJAX: Get paginated users with filters.
     */
    public function getUsers(Request $request)
    {
        $query = User::where('role', 'user');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($users);
    }

    /**
     * AJAX: Update user status (active/inactive).
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending'
        ]);

        $user = User::findOrFail($id);
        
        // Prevent deactivating oneself
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account.'
            ], 422);
        }

        $oldValues = $user->toArray();
        $user->status = $request->status;
        $user->save();

        // Send email notification on account approval
        if ($request->status === 'active' && $oldValues['status'] === 'pending') {
            try {
                if (setting('enable_email_notifications', '1') === '1') {
                    Mail::to($user->email)->send(new AccountApprovedMail($user));
                }
            } catch (\Exception $e) {
                Log::error("Failed to send account approval email to {$user->email}: " . $e->getMessage());
            }
        }

        AuditService::log(
            Auth::id(),
            'update_user_status',
            'User',
            $id,
            $oldValues,
            $user->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.',
            'user' => $user
        ]);
    }

    /**
     * AJAX: Update user role (user/admin).
     */
    public function updateRole(Request $request, int $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin,staff'
        ]);

        $user = User::findOrFail($id);

        // Prevent changing oneself's role
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role.'
            ], 422);
        }

        $oldValues = $user->toArray();
        $user->role = $request->role;
        $user->save();

        AuditService::log(
            Auth::id(),
            'update_user_role',
            'User',
            $id,
            $oldValues,
            $user->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.',
            'user' => $user
        ]);
    }

    /**
     * AJAX: Delete user.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 422);
        }

        $oldValues = $user->toArray();
        $user->delete();

        AuditService::log(
            Auth::id(),
            'delete_user',
            'User',
            $id,
            $oldValues,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    /**
     * Display staff management view.
     */
    public function staffIndex()
    {
        return view('admin.staff');
    }

    /**
     * AJAX: Get paginated staff list with active complaints count.
     */
    public function getStaffList(Request $request)
    {
        $query = User::where('role', 'staff')
            ->withCount(['assignedComplaints' => function($q) {
                $q->whereIn('status', ['Pending', 'In Progress']);
            }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $staff = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($staff);
    }

    /**
     * AJAX: Create new staff user directly by admin.
     */
    public function createStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:15',
            'department' => 'required|string|max:100',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'phone' => $request->phone,
            'department' => $request->department,
            'role' => 'staff',
            'status' => 'active',
        ]);

        // Send Welcome Email
        try {
            if (setting('enable_email_notifications', '1') === '1') {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send welcome email to created staff {$user->email}: " . $e->getMessage());
        }

        // Audit Log
        AuditService::log(
            Auth::id(),
            'create_staff',
            'User',
            $user->id,
            null,
            $user->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully.',
            'user' => $user
        ]);
    }
}
