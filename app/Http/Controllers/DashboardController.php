<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Department;
use App\Models\AuditType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameter
        $departmentFilter = $request->get('department', 'all');
        
        // Get all departments for dropdown
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Statistics based on user role and filter
        $stats = $this->getStatsByRole($user, $departmentFilter);
        
        // Recent reports
        $recentReports = $this->getRecentReports($user, $departmentFilter);
        
        // Chart data for Pie Chart
        $chartData = $this->getChartData($user, $departmentFilter);
        
        return view('dashboard', compact('stats', 'recentReports', 'chartData', 'departments', 'departmentFilter'));
    }
    
    private function getStatsByRole($user, $departmentFilter = 'all')
    {
        $stats = [];
        
        switch ($user->role) {
            case 'super_admin':
                $query = Report::query();
                
                // Apply department filter
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                
                $stats = [
                    'total_reports' => (clone $query)->count(),
                    'pending' => (clone $query)->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'completed' => (clone $query)->where('status', 'fixed')->count(),
                    'approved' => (clone $query)->where('status', 'approved')->count(),
                ];
                break;
                
            case 'auditor':
                $query = Report::where('auditor_id', $user->id);
                
                // Apply department filter
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                
                $stats = [
                    'my_reports' => (clone $query)->count(),
                    'pending' => (clone $query)->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'need_review' => (clone $query)->where('status', 'fixed')->count(),
                    'approved' => (clone $query)->where('status', 'approved')->count(),
                ];
                break;
                
            case 'staff_departemen':
                $query = Report::where('department_id', $user->department_id);
                
                $stats = [
                    'assigned' => (clone $query)->count(),
                    'pending' => (clone $query)->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'fixed' => (clone $query)->where('status', 'fixed')->count(),
                    'approved' => (clone $query)->where('status', 'approved')->count(),
                ];
                break;
        }
        
        return $stats;
    }
    
    private function getRecentReports($user, $departmentFilter = 'all')
    {
        $query = Report::with(['auditType', 'department', 'auditor']);
        
        switch ($user->role) {
            case 'super_admin':
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                break;
            case 'auditor':
                $query->where('auditor_id', $user->id);
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                break;
            case 'staff_departemen':
                $query->where('department_id', $user->department_id);
                break;
        }
        
        return $query->latest()->take(5)->get();
    }
    
    private function getChartData($user, $departmentFilter = 'all')
    {
        // Base query based on role
        $query = Report::query();
        
        switch ($user->role) {
            case 'super_admin':
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                break;
            case 'auditor':
                $query->where('auditor_id', $user->id);
                if ($departmentFilter !== 'all') {
                    $query->where('department_id', $departmentFilter);
                }
                break;
            case 'staff_departemen':
                $query->where('department_id', $user->department_id);
                break;
        }
        
        // Status distribution for Pie Chart
        $statusData = (clone $query)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // Ensure all statuses are present (even if count is 0)
        $allStatuses = [
            'submitted' => $statusData['submitted'] ?? 0,
            'in_progress' => $statusData['in_progress'] ?? 0,
            'fixed' => $statusData['fixed'] ?? 0,
            'approved' => $statusData['approved'] ?? 0,
        ];
        
        // Calculate percentages
        $total = array_sum($allStatuses);
        $percentages = [];
        foreach ($allStatuses as $status => $count) {
            $percentages[$status] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }
        
        return [
            'labels' => ['Submitted', 'In Progress', 'Fixed', 'Approved'],
            'data' => array_values($allStatuses),
            'percentages' => array_values($percentages),
            'colors' => ['#3498db', '#f39c12', '#27ae60', '#9b59b6'], // Blue, Yellow, Green, Purple
        ];
    }
}