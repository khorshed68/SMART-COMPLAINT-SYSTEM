<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
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
            'status' => 'required|in:active,inactive'
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
            'role' => 'required|in:user,admin'
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
}
