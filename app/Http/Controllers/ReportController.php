<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Query berdasarkan role
        $query = Report::with(['department', 'auditType', 'auditor']);
        
        if ($user->role === 'staff_departemen') {
            $query->where('department_id', $user->department_id);
        } elseif ($user->role === 'auditor') {
            $query->where('auditor_id', $user->id);
        }
        // super_admin melihat semua
        
        // Filter Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('report_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('issue_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter Status - INI YANG DIPERBAIKI
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter Department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        
        // Filter Period
        if ($request->filled('period')) {
            $now = now();
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $now->copy()->startOfWeek(),
                        $now->copy()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }
        
        // Filter Custom Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Order dan Pagination
        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Kirim data departments dan auditTypes untuk filter
        $departments = \App\Models\Department::orderBy('name')->get();
        $auditTypes = \App\Models\AuditType::orderBy('name')->get();
        
        return view('reports.index', compact('reports', 'departments', 'auditTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = \App\Models\Department::all();
        
        return view('reports.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'department_id.required' => 'Department must be selected.',
            'location.required' => 'Location is required.',
            'issue_type.required' => 'Issue type is required.',
            'description.required' => 'Description is required.',
            'photos.required' => 'Photos must be uploaded.',
            'photos.min' => 'At least 1 photo is required.',
            'photos.*.image' => 'File must be an image.',
            'photos.*.mimes' => 'Image format must be jpeg, png, or jpg.',
            'photos.*.max' => 'Maximum image size is 5MB.'
        ]);

        // Upload photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reports', 'public');
                $photoPaths[] = $path;
            }
        }

        // Generate report number
        $reportNumber = 'RPT-' . date('Ymd') . '-' . str_pad(Report::count() + 1, 4, '0', STR_PAD_LEFT);

        // Ambil user yang login
        $user = auth()->user();
        
        // Ambil audit_type_id dari user atau gunakan default (audit type pertama)
        $auditTypeId = $user->audit_type_id ?? \App\Models\AuditType::first()->id;

        // Create report
        Report::create([
            'report_number' => $reportNumber,
            'department_id' => $validated['department_id'],
            'audit_type_id' => $auditTypeId,
            'auditor_id' => $user->id,
            'location' => $validated['location'],
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'photos' => json_encode($photoPaths),
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = Report::with(['department', 'auditType', 'auditor', 'responses.user'])
            ->findOrFail($id);
        
        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);
        $departments = \App\Models\Department::all();
        $auditTypes = \App\Models\AuditType::all();
        
        return view('reports.edit', compact('report', 'departments', 'auditTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $validated = $request->validate([
            'audit_type_id' => 'required|exists:audit_types,id',
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        // Upload new photos if provided
        if ($request->hasFile('photos')) {
            // Keep old photos and add new ones
            $oldPhotos = json_decode($report->photos, true) ?? [];
            
            // Upload new photos
            $newPhotoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reports', 'public');
                $newPhotoPaths[] = $path;
            }
            
            // Combine old and new photos
            $validated['photos'] = json_encode(array_merge($oldPhotos, $newPhotoPaths));
        }

        $report->update($validated);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        // Delete photos
        $photos = json_decode($report->photos, true);
        if ($photos) {
            foreach ($photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Mulai progress audit dengan deadline
     */
    public function startProgress(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi input deadline
        $validated = $request->validate([
            'deadline' => 'required|date|after:today',
            'deadline_reason' => 'required|string|min:5'
        ], [
            'deadline.required' => 'Deadline date is required.',
            'deadline.date' => 'Invalid date format.',
            'deadline.after' => 'Deadline must be after today.',
            'deadline_reason.required' => 'Deadline reason is required.',
            'deadline_reason.min' => 'Deadline reason must be at least 5 characters.'
        ]);

        // Update status dan deadline
        $report->status = 'in_progress';
        $report->started_at = now();
        $report->deadline = $validated['deadline'];
        $report->deadline_reason = $validated['deadline_reason'];
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('success', 'Report progress started. Deadline: ' . \Carbon\Carbon::parse($validated['deadline'])->format('d F Y'));
    }

    /**
     * Staff departemen memberikan response/tanggapan
     */
    public function respond(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi
        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 10 characters.',
            'photos.required' => 'Repair photos must be uploaded.',
            'photos.min' => 'At least 1 photo is required.',
            'photos.*.image' => 'File must be an image.',
            'photos.*.mimes' => 'Image format must be jpeg, png, or jpg.',
            'photos.*.max' => 'Maximum image size is 5MB.'
        ]);
        
        // Upload photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('report-responses', 'public');
                $photoPaths[] = $path;
            }
        }
        
        // Simpan response
        ReportResponse::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'description' => $validated['description'],
            'photos' => json_encode($photoPaths)
        ]);
        
        // Update status report menjadi 'fixed'
        $report->status = 'fixed';
        $report->fixed_at = now();
        $report->rejection_reason = null; // Clear rejection reason when fixed
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('success', 'Response submitted successfully. Report status changed to Fixed.');
    }

    /**
     * Auditor approve report
     */
    public function approve($id)
    {
        $report = Report::findOrFail($id);
        
        // Validate status
        if ($report->status !== 'fixed') {
            return redirect()->route('reports.show', $id)
                ->with('error', 'Only reports with status "Fixed" can be approved.');
        }
        
        // Update status
        $report->status = 'approved';
        $report->approved_at = now();
        $report->approved_by = auth()->id();
        $report->rejection_reason = null; // Clear rejection reason
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('success', 'Report has been approved successfully!');
    }

    /**
     * Auditor reject report - Professional rejection handling
     * Status changes to 'rejected' for revision
     */
    public function reject(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ], [
            'rejection_reason.required' => 'Rejection reason is required.',
            'rejection_reason.min' => 'Rejection reason must be at least 10 characters.',
            'rejection_reason.max' => 'Rejection reason maximum 500 characters.'
        ]);
        
        // Update status to 'rejected'
        $report->status = 'rejected';
        $report->rejection_reason = $validated['rejection_reason'];
        $report->approved_at = null;
        $report->fixed_at = null; // Reset fixed_at because it needs to be fixed again
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('warning', 'Report rejected and sent back for revision. The department has been notified.');
    }
}