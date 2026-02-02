<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_number',
        'audit_type_id',
        'department_id',
        'auditor_id',
        'location',
        'issue_type',
        'description',
        'photos',
        'status',
        'rejection_reason',
        'submitted_at',
        'fixed_at',
        'approved_at',
        'approved_by',
        'started_at',
        'deadline',
    ];

    // Cast untuk convert otomatis JSON ke array dan string ke datetime
    protected $casts = [
        'photos' => 'array',
        'submitted_at' => 'datetime',
        'fixed_at' => 'datetime',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'deadline' => 'date',
    ];

    // Relationships
    public function responses()
    {
        return $this->hasMany(ReportResponse::class);
    }

    public function auditType()
    {
        return $this->belongsTo(AuditType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'submitted' => '<span class="badge bg-primary">Submitted</span>',
            'in_progress' => '<span class="badge bg-warning">In Progress</span>',
            'fixed' => '<span class="badge bg-info">Fixed</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'submitted' => 'Terkirim',
            'in_progress' => 'Dalam Proses',
            'fixed' => 'Selesai Diperbaiki',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByDepartment($query, $departmentId)
    {
        if ($departmentId) {
            return $query->where('department_id', $departmentId);
        }
        return $query;
    }

    public function scopeByPeriod($query, $period)
    {
        switch ($period) {
            case 'week':
                return $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            case 'month':
                return $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
            case 'year':
                return $query->whereYear('created_at', now()->year);
            default:
                return $query;
        }
    }

    public function scopeByDateRange($query, $dateFrom, $dateTo)
    {
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('report_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('issue_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}