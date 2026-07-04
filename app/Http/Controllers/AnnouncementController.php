<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements to admins.
     */
    public function index()
    {
        $announcements = Announcement::with(['category', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.announcements.index', compact('announcements', 'categories'));
    }

    /**
     * Store a newly created announcement in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'created_by' => Auth::id(),
        ]);

        // Audit Log
        AuditService::log(
            Auth::id(),
            'announcement_created',
            'announcement',
            $announcement->id,
            null,
            ['title' => $announcement->title, 'category_id' => $announcement->category_id]
        );

        return redirect()->back()->with('success', 'Announcement scheduled successfully!');
    }

    /**
     * Remove an announcement.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        // Audit Log
        AuditService::log(
            Auth::id(),
            'announcement_deleted',
            'announcement',
            $id,
            null,
            null
        );

        return redirect()->back()->with('success', 'Announcement removed successfully!');
    }

    /**
     * API: Get active announcements, optionally filtered by category.
     */
    public function active(Request $request)
    {
        $query = Announcement::active()->with('category');

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where(function($q) use ($request) {
                $q->where('category_id', $request->category_id)
                  ->orWhereNull('category_id');
            });
        }

        $activeAnnouncements = $query->orderBy('start_time', 'asc')->get();

        return response()->json($activeAnnouncements);
    }

    /**
     * Display general bulletin board to users.
     */
    public function userIndex()
    {
        $announcements = Announcement::active()
            ->with(['category', 'creator'])
            ->orderBy('start_time', 'desc')
            ->paginate(8);

        return view('user.announcements.index', compact('announcements'));
    }

    /**
     * Display details of a single active announcement.
     */
    public function userShow($id)
    {
        $announcement = Announcement::active()
            ->with(['category', 'creator'])
            ->findOrFail($id);

        return view('user.announcements.show', compact('announcement'));
    }
}
