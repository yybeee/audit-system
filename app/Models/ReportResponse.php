<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportResponse extends Model
{
    protected $fillable = [
        'report_id',
        'user_id',
        'description',
        'photos'
    ];

    protected $casts = [
        'photos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}