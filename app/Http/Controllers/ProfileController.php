<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show profile management view.
     */
    public function show()
    {
        return view('user.profile');
    }

    /**
     * AJAX: Get profile data of current user.
     */
    public function getProfile()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    /**
     * AJAX: Update user profile.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $oldValues = $user->toArray();

        $user->update($request->validated());

        AuditService::log(
            $user->id,
            'update_profile',
            'User',
            $user->id,
            $oldValues,
            $user->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $user
        ]);
    }

    /**
     * AJAX: Change user password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match.'
            ], 422);
        }

        $oldValues = ['password' => '[HIDDEN]'];
        $user->password = Hash::make($request->new_password);
        $user->save();

        AuditService::log(
            $user->id,
            'change_password',
            'User',
            $user->id,
            $oldValues,
            ['password' => '[HIDDEN]']
        );

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    /**
     * AJAX: Upload user avatar image.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $user = Auth::user();
        $oldValues = $user->toArray();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Save new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            
            $user->avatar = $path;
            $user->save();

            AuditService::log(
                $user->id,
                'upload_avatar',
                'User',
                $user->id,
                $oldValues,
                $user->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully.',
                'avatar_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded.'
        ], 400);
    }
}
