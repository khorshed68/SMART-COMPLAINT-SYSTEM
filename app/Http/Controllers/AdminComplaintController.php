<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStatusRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminComplaintController extends Controller
{
    protected $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    /**
     * Admin dashboard view.
     */
    public function dashboard()
    {
        $admins = User::admins()->active()->get();
        return view('admin.dashboard', compact('admins'));
    }

    /**
     * Admin complaints list view.
     */
    public function index()
    {
        $admins = User::admins()->active()->get();
        return view('admin.complaints', compact('admins'));
    }

    /**
     * Admin complaint details panel view.
     */
    public function show(int $id)
    {
        $complaint = Complaint::with(['user', 'category', 'assignee'])->findOrFail($id);
        $admins = User::admins()->active()->get();
        return view('admin.complaint-detail', compact('complaint', 'admins'));
    }

    /**
     * AJAX: Update status of a complaint.
     */
    public function updateStatus(UpdateStatusRequest $request, int $id)
    {
        $complaint = $this->complaintService->updateStatus(
            $id,
            $request->status,
            Auth::id(),
            $request->comment
        );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'complaint' => $complaint
        ]);
    }

    /**
     * AJAX: Assign complaint to an admin user.
     */
    public function assign(Request $request, int $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $complaint = $this->complaintService->assignComplaint(
            $id,
            Auth::id(),
            (int) $request->assigned_to
        );

        return response()->json([
            'success' => true,
            'message' => 'Complaint assigned successfully.',
            'complaint' => $complaint
        ]);
    }

    /**
     * AJAX: Change priority of a complaint.
     */
    public function changePriority(Request $request, int $id)
    {
        $request->validate([
            'priority' => 'required|in:Low,Medium,High',
        ]);

        $complaint = $this->complaintService->changePriority(
            $id,
            Auth::id(),
            $request->priority
        );

        return response()->json([
            'success' => true,
            'message' => 'Priority changed successfully.',
            'complaint' => $complaint
        ]);
    }

    /**
     * AJAX: Add timeline comment.
     */
    public function addComment(Request $request, int $id)
    {
        $request->validate([
            'comment' => 'required|string|min:1',
        ]);

        $update = $this->complaintService->addComment(
            $id,
            Auth::id(),
            $request->comment
        );

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully.',
            'update' => $update
        ]);
    }

    /**
     * AJAX: Delete a complaint.
     */
    public function destroy(int $id)
    {
        $complaint = Complaint::findOrFail($id);
        $oldValues = $complaint->toArray();

        // Delete attachment if exists
        if ($complaint->attachment) {
            Storage::disk('public')->delete($complaint->attachment);
        }

        $complaint->delete();

        // Audit Log
        AuditService::log(
            Auth::id(),
            'delete_complaint',
            'Complaint',
            $id,
            $oldValues,
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully.'
        ]);
    }
}
