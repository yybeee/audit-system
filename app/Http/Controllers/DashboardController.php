<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Department;
use App\Models\AuditType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistics based on user role
        $stats = $this->getStatsByRole($user);
        
        // Recent reports
        $recentReports = $this->getRecentReports($user);
        
        // Chart data
        $chartData = $this->getChartData($user);
        
        return view('dashboard', compact('stats', 'recentReports', 'chartData'));
    }
    
    private function getStatsByRole($user)
    {
        $stats = [];
        
        switch ($user->role) {
            case 'super_admin':
                $stats = [
                    'total_reports' => Report::count(),
                    'pending' => Report::whereIn('status', ['submitted', 'in_progress'])->count(),
                    'completed' => Report::where('status', 'approved')->count(),
                    'departments' => Department::where('is_active', true)->count(),
                ];
                break;
                
            case 'auditor':
                $stats = [
                    'my_reports' => Report::where('auditor_id', $user->id)->count(),
                    'pending' => Report::where('auditor_id', $user->id)
                        ->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'need_review' => Report::where('auditor_id', $user->id)
                        ->where('status', 'fixed')->count(),
                    'completed' => Report::where('auditor_id', $user->id)
                        ->where('status', 'approved')->count(),
                ];
                break;
                
            case 'staff_departemen':
                $stats = [
                    'assigned' => Report::where('department_id', $user->department_id)->count(),
                    'pending' => Report::where('department_id', $user->department_id)
                        ->whereIn('status', ['submitted', 'in_progress'])->count(),
                    'fixed' => Report::where('department_id', $user->department_id)
                        ->where('status', 'fixed')->count(),
                    'approved' => Report::where('department_id', $user->department_id)
                        ->where('status', 'approved')->count(),
                ];
                break;
        }
        
        return $stats;
    }
    
    private function getRecentReports($user)
    {
        $query = Report::with(['auditType', 'department', 'auditor']);
        
        switch ($user->role) {
            case 'auditor':
                $query->where('auditor_id', $user->id);
                break;
            case 'staff_departemen':
                $query->where('department_id', $user->department_id);
                break;
        }
        
        return $query->latest()->take(5)->get();
    }
    
    private function getChartData($user)
    {
        // Status distribution
        $statusData = Report::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        // Department distribution (for admin)
        $departmentData = [];
        if ($user->role === 'super_admin') {
            $departmentData = Report::selectRaw('department_id, COUNT(*) as count')
                ->groupBy('department_id')
                ->with('department')
                ->get()
                ->pluck('count', 'department.name')
                ->toArray();
        }
        
        return [
            'status' => $statusData,
            'departments' => $departmentData,
        ];
    }
}