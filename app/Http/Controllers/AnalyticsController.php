<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Analytics index page.
     */
    public function index()
    {
        return view('admin.analytics');
    }

    /**
     * AJAX: Get overview statistics.
     */
    public function getOverview()
    {
        return response()->json($this->analyticsService->getOverallStats());
    }

    /**
     * AJAX: Get daily/monthly trends.
     */
    public function getTrends(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $count = (int) $request->query('count', $period === 'monthly' ? 12 : 30);

        $trends = $period === 'monthly'
            ? $this->analyticsService->getMonthlyTrends($count)
            : $this->analyticsService->getDailyTrends($count);

        return response()->json($trends);
    }

    /**
     * AJAX: Get category distributions.
     */
    public function getCategoryStats()
    {
        return response()->json($this->analyticsService->getCategoryDistribution());
    }

    /**
     * AJAX: Get resolution stats + assignee performance.
     */
    public function getResolutionStats()
    {
        return response()->json([
            'resolution_time' => $this->analyticsService->getResolutionTimeStats(),
            'assignee_performance' => $this->analyticsService->getAssigneePerformance()
        ]);
    }

    /**
     * AJAX: Get hourly/weekday distributions.
     */
    public function getDistribution(Request $request)
    {
        $type = $request->query('type', 'hourly');

        $distribution = $type === 'hourly'
            ? $this->analyticsService->getHourlyDistribution()
            : $this->analyticsService->getWeekdayDistribution();

        return response()->json($distribution);
    }

    /**
     * AJAX/Download: Export filtered complaints.
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');
        $complaints = $this->analyticsService->exportComplaints($request->all());

        // Audit Log
        AuditService::log(
            Auth::id(),
            'export_complaints',
            'Complaint',
            null,
            null,
            $request->all()
        );

        if ($format === 'json') {
            return response()->json($complaints);
        }

        // Export as CSV
        $fileName = 'complaints_export_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Complaint ID', 'Title', 'Description', 'Category', 'Priority', 
            'Status', 'Submitter Name', 'Submitter Email', 'Department', 
            'Location', 'Assigned To', 'Created At', 'Resolved At', 'Resolution Notes'
        ];

        $callback = function() use($complaints, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($complaints as $complaint) {
                fputcsv($file, [
                    $complaint->id,
                    $complaint->title,
                    $complaint->description,
                    $complaint->category->name ?? 'N/A',
                    $complaint->priority,
                    $complaint->status,
                    $complaint->user->name ?? 'N/A',
                    $complaint->user->email ?? 'N/A',
                    $complaint->user->department ?? 'N/A',
                    $complaint->location ?? 'N/A',
                    $complaint->assignee->name ?? 'Unassigned',
                    $complaint->created_at->format('Y-m-d H:i:s'),
                    $complaint->resolved_at ? $complaint->resolved_at->format('Y-m-d H:i:s') : 'N/A',
                    $complaint->resolution_notes ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
