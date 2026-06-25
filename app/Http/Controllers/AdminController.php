<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Show control panel view.
     */
    public function controlPanel()
    {
        return view('admin.control-panel');
    }

    /**
     * Show admin change password view.
     */
    public function changePassword()
    {
        return view('admin.change-password');
    }

    /**
     * Show system health dashboard view.
     */
    public function systemHealth()
    {
        return view('admin.system-health');
    }

    /**
     * AJAX: Get paginated audit logs with filtering.
     */
    public function getAuditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($logs);
    }

    /**
     * AJAX: Retrieve system health parameters.
     */
    public function getSystemHealth()
    {
        $phpVersion = PHP_VERSION;
        
        $mysqlVersion = 'N/A';
        try {
            $results = DB::select("SELECT VERSION() as version");
            $mysqlVersion = $results[0]->version ?? 'N/A';
        } catch (\Exception $e) {
            Log::error("Failed to query MySQL version: " . $e->getMessage());
        }

        // Disk details
        $ds = DIRECTORY_SEPARATOR;
        $rootPath = base_path();
        $freeSpace = @disk_free_space($rootPath) ?: 0;
        $totalSpace = @disk_total_space($rootPath) ?: 1; // avoid divide by zero
        $usedSpace = $totalSpace - $freeSpace;
        $diskPercentage = round(($usedSpace / $totalSpace) * 100, 1);

        // Memory details (in-system approximation or current script memory)
        $memUsage = memory_get_usage(true);
        $memLimit = ini_get('memory_limit');
        
        // Convert ini shorthand (e.g. 128M, 2G) to bytes
        $limitBytes = $this->convertToBytes($memLimit);
        $memPercentage = $limitBytes > 0 ? round(($memUsage / $limitBytes) * 100, 1) : 0;

        return response()->json([
            'php_version' => $phpVersion,
            'mysql_version' => $mysqlVersion,
            'disk' => [
                'free' => $this->formatBytes($freeSpace),
                'total' => $this->formatBytes($totalSpace),
                'used' => $this->formatBytes($usedSpace),
                'percentage' => $diskPercentage,
            ],
            'memory' => [
                'used' => $this->formatBytes($memUsage),
                'limit' => $memLimit,
                'percentage' => $memPercentage,
            ],
            'uptime' => $this->getSystemUptime(),
            'database_status' => 'Healthy',
            'storage_status' => is_writable(storage_path()) ? 'Writable' : 'Locked'
        ]);
    }

    /**
     * Helper: Convert php.ini memory limits to bytes.
     */
    private function convertToBytes(string $val): int
    {
        $val = trim($val);
        if ($val === '-1' || empty($val)) {
            return 512 * 1024 * 1024; // default limit assumption if unlimited
        }
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Helper: Format bytes to human readable form.
     */
    private function formatBytes(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Helper: Fetch uptime approximation.
     */
    private function getSystemUptime(): string
    {
        // Simple uptime calculation check on windows vs unix
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows uptime check via command
            return 'Running on Windows OS';
        }

        // Unix uptime
        $str = @file_get_contents('/proc/uptime');
        if ($str === false) {
            return 'N/A';
        }
        $num = (float)$str;
        $secs = $num % 60;
        $mins = ($num / 60) % 60;
        $hours = ($num / 3600) % 24;
        $days = (int)($num / 86400);

        return "{$days}d {$hours}h {$mins}m";
    }
}
