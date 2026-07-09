<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Services\ComplaintService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    /**
     * Display staff dashboard overview.
     */
    public function dashboard()
    {
        $staffId = Auth::id();

        // Get counts
        $stats = Complaint::where('assigned_to', $staffId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved
            ")->first();

        return view('staff.dashboard', compact('stats'));
    }

    /**
     * AJAX: Get paginated assigned complaints list with filtering.
     */
    public function getComplaints(Request $request)
    {
        $staffId = Auth::id();

        /** @var \Illuminate\Database\Eloquent\Builder|\App\Models\Complaint $query */
        $query = Complaint::where('assigned_to', $staffId)->with(['user', 'category']);

        // Apply filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = (int) setting('complaints_per_page', '10');
        $complaints = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        return response()->json($complaints);
    }

    /**
     * Display single complaint detail view.
     */
    public function show(int $id)
    {
        $staffId = Auth::id();
        $complaint = Complaint::with(['category', 'user', 'updates.updater'])->findOrFail($id);

        // Security check: Only assigned staff can view
        if ($complaint->assigned_to !== $staffId) {
            abort(403, 'Unauthorized action. This complaint is not assigned to you.');
        }

        return view('staff.complaint-detail', compact('complaint'));
    }

    /**
     * AJAX: Update status of an assigned complaint.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:In Progress,Resolved',
            'comment' => 'nullable|string|max:1000'
        ]);

        $staffId = Auth::id();
        $complaint = Complaint::findOrFail($id);

        // Security check
        if ($complaint->assigned_to !== $staffId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Perform update via ComplaintService
        $this->complaintService->updateStatus($id, $request->status, $staffId, $request->comment);

        return response()->json([
            'success' => true,
            'message' => 'Complaint status updated successfully.'
        ]);
    }

    /**
     * AJAX: Add timeline comment to an assigned complaint.
     */
    public function addComment(Request $request, int $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $staffId = Auth::id();
        $complaint = Complaint::findOrFail($id);

        // Security check
        if ($complaint->assigned_to !== $staffId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $update = ComplaintUpdate::create([
            'complaint_id' => $id,
            'updated_by' => $staffId,
            'old_status' => $complaint->status,
            'new_status' => $complaint->status,
            'comment' => $request->comment,
            'update_type' => 'comment',
            'created_at' => now(),
        ]);

        // Audit Log
        AuditService::log(
            $staffId,
            'add_comment',
            'Complaint',
            $id,
            null,
            ['comment' => $request->comment]
        );

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully.',
            'update' => $update->load('updater')
        ]);
    }
}
