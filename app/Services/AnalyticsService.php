<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get overall statistics.
     */
    public function getOverallStats(): array
    {
        $now = Carbon::now();
        
        $complaintStats = Complaint::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();

        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalCategories = Category::count();

        // Calculate average resolution days
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $avgResolutionDays = Complaint::status('Resolved')
                ->whereNotNull('resolved_at')
                ->selectRaw("AVG(julianday(resolved_at) - julianday(created_at)) as avg_days")
                ->value('avg_days') ?? 0;
        } else {
            $avgResolutionDays = Complaint::status('Resolved')
                ->whereNotNull('resolved_at')
                ->selectRaw("AVG(TIMESTAMPDIFF(DAY, created_at, resolved_at)) as avg_days")
                ->value('avg_days') ?? 0;
        }

        $avgSatisfaction = Complaint::whereNotNull('rating')
            ->avg('rating') ?? 0;
        $totalRated = Complaint::whereNotNull('rating')->count();

        $complaintsToday = Complaint::whereDate('created_at', Carbon::today())->count();
        $complaintsThisWeek = Complaint::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $complaintsThisMonth = Complaint::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        return [
            'total_complaints' => (int) ($complaintStats->total ?? 0),
            'pending' => (int) ($complaintStats->pending ?? 0),
            'in_progress' => (int) ($complaintStats->in_progress ?? 0),
            'resolved' => (int) ($complaintStats->resolved ?? 0),
            'rejected' => (int) ($complaintStats->rejected ?? 0),
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_categories' => $totalCategories,
            'avg_resolution_days' => round($avgResolutionDays, 1),
            'avg_satisfaction' => round($avgSatisfaction, 1),
            'total_rated' => $totalRated,
            'complaints_today' => $complaintsToday,
            'complaints_this_week' => $complaintsThisWeek,
            'complaints_this_month' => $complaintsThisMonth,
        ];
    }

    /**
     * Get complaints count and resolution rate by category.
     */
    public function getCategoryDistribution(): array
    {
        return Category::leftJoin('complaints', 'categories.id', '=', 'complaints.category_id')
            ->select('categories.name', 'categories.color')
            ->selectRaw('COUNT(complaints.id) as total_count')
            ->selectRaw("SUM(CASE WHEN complaints.status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count")
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->get()
            ->map(function ($item) {
                $rate = $item->total_count > 0 ? ($item->resolved_count / $item->total_count) * 100 : 0;
                return [
                    'name' => $item->name,
                    'color' => $item->color,
                    'total' => (int) $item->total_count,
                    'resolved' => (int) $item->resolved_count,
                    'rate' => round($rate, 1),
                ];
            })
            ->toArray();
    }

    /**
     * Get complaints count by priority.
     */
    public function getPriorityDistribution(): array
    {
        return Complaint::select('priority')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }

    /**
     * Get complaints count by status.
     */
    public function getStatusDistribution(): array
    {
        return Complaint::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get monthly trends (last $months).
     */
    public function getMonthlyTrends(int $months = 12): array
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        // Base series generated in PHP to ensure months with 0 complaints are present
        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $key = $monthDate->format('Y-m');
            $trends[$key] = [
                'year' => $monthDate->format('Y'),
                'month' => $monthDate->format('M'),
                'total' => 0,
                'resolved' => 0,
            ];
        }

        $data = Complaint::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count
            ")
            ->where('created_at', '>=', $startDate)
            ->groupBy('month_key')
            ->get();

        foreach ($data as $row) {
            if (isset($trends[$row->month_key])) {
                $trends[$row->month_key]['total'] = (int) $row->total_count;
                $trends[$row->month_key]['resolved'] = (int) $row->resolved_count;
            }
        }

        return array_values($trends);
    }

    /**
     * Get daily trends (last $days).
     */
    public function getDailyTrends(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $trends = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $trends[$date] = [
                'date' => $date,
                'total' => 0
            ];
        }

        $data = Complaint::selectRaw("
                DATE(created_at) as date_key,
                COUNT(*) as total_count
            ")
            ->where('created_at', '>=', $startDate)
            ->groupBy('date_key')
            ->get();

        foreach ($data as $row) {
            if (isset($trends[$row->date_key])) {
                $trends[$row->date_key]['total'] = (int) $row->total_count;
            }
        }

        return array_values($trends);
    }

    /**
     * Get resolution time stats.
     */
    public function getResolutionTimeStats(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $stats = Complaint::status('Resolved')
                ->whereNotNull('resolved_at')
                ->selectRaw("
                    AVG((julianday(resolved_at) - julianday(created_at)) * 24) as avg_hours,
                    MIN((julianday(resolved_at) - julianday(created_at)) * 24) as min_hours,
                    MAX((julianday(resolved_at) - julianday(created_at)) * 24) as max_hours
                ")
                ->first();
        } else {
            $stats = Complaint::status('Resolved')
                ->whereNotNull('resolved_at')
                ->selectRaw("
                    AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours,
                    MIN(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as min_hours,
                    MAX(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as max_hours
                ")
                ->first();
        }

        return [
            'avg_hours' => round($stats->avg_hours ?? 0, 1),
            'min_hours' => round($stats->min_hours ?? 0, 1),
            'max_hours' => round($stats->max_hours ?? 0, 1),
        ];
    }

    /**
     * Get assignee performance statistics.
     */
    public function getAssigneePerformance(): array
    {
        $driver = DB::connection()->getDriverName();
        $avgHoursExpr = $driver === 'sqlite'
            ? "AVG(CASE WHEN complaints.status = 'Resolved' THEN (julianday(complaints.resolved_at) - julianday(complaints.created_at)) * 24 ELSE NULL END) as avg_res_hours"
            : "AVG(CASE WHEN complaints.status = 'Resolved' THEN TIMESTAMPDIFF(HOUR, complaints.created_at, complaints.resolved_at) ELSE NULL END) as avg_res_hours";

        return User::admins()
            ->leftJoin('complaints', 'users.id', '=', 'complaints.assigned_to')
            ->select('users.name')
            ->selectRaw('COUNT(complaints.id) as assigned_count')
            ->selectRaw("SUM(CASE WHEN complaints.status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count")
            ->selectRaw($avgHoursExpr)
            ->selectRaw('AVG(complaints.rating) as avg_rating')
            ->selectRaw('COUNT(complaints.rating) as rated_count')
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function ($item) {
                $rate = $item->assigned_count > 0 ? ($item->resolved_count / $item->assigned_count) * 100 : 0;
                return [
                    'name' => $item->name,
                    'assigned' => (int) $item->assigned_count,
                    'resolved' => (int) $item->resolved_count,
                    'avg_hours' => round($item->avg_res_hours ?? 0, 1),
                    'rate' => round($rate, 1),
                    'avg_rating' => $item->avg_rating !== null ? round($item->avg_rating, 1) : null,
                    'rated_count' => (int) $item->rated_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get hourly distribution of complaints (0-23).
     */
    public function getHourlyDistribution(): array
    {
        $hours = array_fill(0, 24, 0);
        $driver = DB::connection()->getDriverName();
        $hourExpr = $driver === 'sqlite' ? "cast(strftime('%H', created_at) as integer) as hour" : "HOUR(created_at) as hour";

        $data = Complaint::selectRaw("$hourExpr, COUNT(*) as count")
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        foreach ($data as $hour => $count) {
            $hours[(int) $hour] = (int) $count;
        }

        return $hours;
    }

    /**
     * Get weekday distribution of complaints.
     */
    public function getWeekdayDistribution(): array
    {
        $days = [
            1 => 0, // Mon
            2 => 0, // Tue
            3 => 0, // Wed
            4 => 0, // Thu
            5 => 0, // Fri
            6 => 0, // Sat
            7 => 0, // Sun
        ];

        $driver = DB::connection()->getDriverName();
        $dayExpr = $driver === 'sqlite' ? "cast(strftime('%w', created_at) as integer) as day" : "WEEKDAY(created_at) as day";

        $data = Complaint::selectRaw("$dayExpr, COUNT(*) as count")
            ->groupBy('day')
            ->get();

        foreach ($data as $row) {
            // strftime('%w') returns 0 = Sunday, 1 = Monday, 6 = Saturday in SQLite
            // WEEKDAY returns 0 = Monday, 6 = Sunday in MySQL
            if ($driver === 'sqlite') {
                $dayNum = (int)$row->day === 0 ? 7 : (int)$row->day;
            } else {
                $dayNum = (int)$row->day + 1;
            }
            $days[$dayNum] = (int) $row->count;
        }

        return array_values($days);
    }

    /**
     * Fetch filtered complaints for export.
     */
    public function exportComplaints(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Complaint::with(['user', 'category', 'assignee']);

        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->priority($filters['priority']);
        }
        if (!empty($filters['category_id'])) {
            $query->category((int) $filters['category_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['date_from']));
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get satisfaction rating distribution (1-5 stars count).
     */
    public function getSatisfactionDistribution(): array
    {
        $distribution = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        $ratings = Complaint::whereNotNull('rating')
            ->select('rating')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        foreach ($ratings as $stars => $count) {
            $distribution[(int) $stars] = (int) $count;
        }

        return $distribution;
    }

    /**
     * Get average satisfaction rating per category.
     */
    public function getCategorySatisfactionDistribution(): array
    {
        return Category::leftJoin('complaints', 'categories.id', '=', 'complaints.category_id')
            ->select('categories.name', 'categories.color')
            ->selectRaw('AVG(complaints.rating) as avg_rating')
            ->selectRaw('COUNT(complaints.rating) as rated_count')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'color' => $item->color,
                    'avg_rating' => $item->avg_rating !== null ? round($item->avg_rating, 1) : null,
                    'rated_count' => (int) $item->rated_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get average satisfaction rating per administrator.
     */
    public function getAdminSatisfactionDistribution(): array
    {
        return User::admins()
            ->leftJoin('complaints', 'users.id', '=', 'complaints.assigned_to')
            ->select('users.name')
            ->selectRaw('AVG(complaints.rating) as avg_rating')
            ->selectRaw('COUNT(complaints.rating) as rated_count')
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'avg_rating' => $item->avg_rating !== null ? round($item->avg_rating, 1) : null,
                    'rated_count' => (int) $item->rated_count,
                ];
            })
            ->toArray();
    }
}
