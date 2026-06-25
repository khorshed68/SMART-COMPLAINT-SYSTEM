<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\Category;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    protected $complaintService;

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

        $recentComplaints = Complaint::byUser($userId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentComplaints'));
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
        if ($complaint->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.complaint-detail', compact('complaint'));
    }

    /**
     * AJAX: Get paginated complaints list with filtering.
     */
    public function getComplaints(Request $request)
    {
        $user = Auth::user();
        
        // Admins can query all complaints; standard users only query their own
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

        if ($complaint->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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

        if ($complaint->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
}
