<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\Category;
use App\Services\ComplaintService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    /**
     * Display user dashboard.
     */
    public function dashboard()
    {
        $userId = Auth::id();
        
        $stats = Complaint::byUser($userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved
            ")->first();

        // 1. Complaints submitted this month
        $complaintsThisMonth = Complaint::byUser($userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 2. Complaint Categories stats for Pie Chart
        $categoryStats = Complaint::byUser($userId)
            ->join('categories', 'complaints.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->groupBy('categories.name')
            ->get();

        // 3. Monthly Complaints stats for Bar/Line Chart (last 6 months)
        $monthlyStats = Complaint::byUser($userId)
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('created_at', 'asc')
            ->limit(6)
            ->get();

        // 4. Recent Activity Timeline (Status updates & comments from history)
        $recentActivity = \App\Models\ComplaintUpdate::join('complaints', 'complaint_updates.complaint_id', '=', 'complaints.id')
            ->where('complaints.user_id', $userId)
            ->select('complaint_updates.*', 'complaints.title as complaint_title')
            ->orderBy('complaint_updates.created_at', 'desc')
            ->limit(5)
            ->get();

        // 5. Average Resolution Days
        $avgResolutionSeconds = Complaint::byUser($userId)
            ->where('status', 'Resolved')
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, resolved_at)) as avg_seconds')
            ->first()->avg_seconds;
        $avgResolutionDays = $avgResolutionSeconds ? round($avgResolutionSeconds / 86400, 1) : null;

        $recentComplaints = Complaint::byUser($userId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'stats', 
            'recentComplaints', 
            'complaintsThisMonth', 
            'categoryStats', 
            'monthlyStats', 
            'recentActivity', 
            'avgResolutionDays'
        ));
    }

    /**
     * Show complaint submission form.
     */
    public function create()
    {
        return view('user.submit-complaint');
    }

    /**
     * Store newly submitted complaint.
     */
    public function store(StoreComplaintRequest $request)
    {
        $this->complaintService->createComplaint($request->validated(), Auth::id());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully!',
                'redirect' => route('complaints.index')
            ]);
        }

        return redirect()->route('complaints.index')->with('success', 'Complaint submitted successfully!');
    }

    /**
     * Display complaints list view.
     */
    public function index()
    {
        return view('user.my-complaints');
    }

    /**
     * Show detail page of a single complaint.
     */
    public function show(int $id)
    {
        $complaint = Complaint::with(['category', 'assignee', 'user'])->findOrFail($id);

        // Security check: Only owner or admin can view
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($complaint->user_id !== Auth::id() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.complaint-detail', compact('complaint'));
    }

    /**
     * AJAX: Get paginated complaints list with filtering.
     */
    public function getComplaints(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Admins can query all complaints; standard users only query their own
        /** @var \Illuminate\Database\Eloquent\Builder|\App\Models\Complaint $query */
        $query = $user->isAdmin() 
            ? Complaint::with(['user', 'category', 'assignee']) 
            : Complaint::byUser($user->id)->with(['category', 'assignee']);

        // Apply filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }
        if ($request->filled('category_id')) {
            $query->category((int) $request->category_id);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = (int) setting('complaints_per_page', '10');
        $complaints = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($complaints);
    }

    /**
     * AJAX: Get detail of a single complaint.
     */
    public function getComplaintDetail(int $id)
    {
        $complaint = Complaint::with(['user', 'category', 'assignee'])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($complaint->user_id !== Auth::id() && !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Include CSS color helpers
        $data = $complaint->toArray();
        $data['status_color'] = $complaint->status_color;
        $data['priority_color'] = $complaint->priority_color;

        return response()->json($data);
    }

    /**
     * AJAX: Get timeline updates for a complaint.
     */
    public function getUpdates(int $id)
    {
        $complaint = Complaint::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($complaint->user_id !== Auth::id() && !$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $updates = $complaint->updates()->with('updater')->orderBy('created_at', 'desc')->get();
        return response()->json($updates);
    }

    /**
     * AJAX: Get count stats for current user.
     */
    public function getStats()
    {
        $userId = Auth::id();
        $stats = Complaint::byUser($userId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
            ")->first();

        return response()->json([
            'total' => (int) ($stats->total ?? 0),
            'pending' => (int) ($stats->pending ?? 0),
            'in_progress' => (int) ($stats->in_progress ?? 0),
            'resolved' => (int) ($stats->resolved ?? 0),
            'rejected' => (int) ($stats->rejected ?? 0),
        ]);
    }

    /**
     * AJAX: Retrieve categories list.
     */
    public function getCategories()
    {
        return response()->json(Category::all());
    }

    /**
     * AJAX: Submit user satisfaction rating and feedback.
     */
    public function rate(Request $request, int $id)
    {
        $complaint = Complaint::findOrFail($id);

        // Security check: Submitter/Owner only
        if ($complaint->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Must be resolved
        if ($complaint->status !== 'Resolved') {
            return response()->json(['message' => 'You can only rate resolved complaints.'], 400);
        }

        // Cannot rate twice
        if ($complaint->rating !== null) {
            return response()->json(['message' => 'This complaint has already been rated.'], 400);
        }

        // Validate rating values
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Save review
        $complaint->update([
            'rating' => (int) $request->rating,
            'feedback' => $request->feedback,
            'rated_at' => now(),
        ]);

        // Audit Log
        AuditService::log(
            Auth::id(),
            'complaint_rated',
            'complaint',
            $complaint->id,
            null,
            ['rating' => $complaint->rating, 'feedback' => $complaint->feedback]
        );

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'rating' => $complaint->rating,
            'feedback' => $complaint->feedback
        ]);
    }
}
