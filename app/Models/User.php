<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'department_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'auditor_id');
    }

    public function responses()
    {
        return $this->hasMany(ReportResponse::class);
    }

    public function approvedReports()
    {
        return $this->hasMany(Report::class, 'approved_by');
    }

    public function getRoleLabelAttribute()
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'auditor' => 'Auditor',
            'staff_departemen' => 'Staff Departemen',
        ];
        return $roles[$this->role] ?? $this->role;
    }
}