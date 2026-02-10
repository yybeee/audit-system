<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportResponse;
use App\Models\Department;
use App\Models\AuditType;
use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
            $query->search($request->search);
        }
        
        // Filter Status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        
        // Filter Department
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }
        
        // Filter Period
        if ($request->filled('period')) {
            $query->byPeriod($request->period);
        }
        
        // Filter Custom Date Range
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }
        
        // Order dan Pagination
        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Kirim data departments dan auditTypes untuk filter
        $departments = Department::orderBy('name')->get();
        $auditTypes = AuditType::orderBy('name')->get();
        
        return view('reports.index', compact('reports', 'departments', 'auditTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
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
        
        // Ambil audit_type_id dari user atau gunakan default
        $auditTypeId = $user->audit_type_id ?? AuditType::first()->id;

        // Create report
        Report::create([
            'report_number' => $reportNumber,
            'department_id' => $validated['department_id'],
            'audit_type_id' => $auditTypeId,
            'auditor_id' => $user->id,
            'location' => $validated['location'],
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'photos' => $photoPaths, // Langsung array, karena sudah di-cast
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
        $departments = Department::all();
        $auditTypes = AuditType::all();
        
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
            $oldPhotos = $report->photos ?? [];
            
            // Upload new photos
            $newPhotoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reports', 'public');
                $newPhotoPaths[] = $path;
            }
            
            // Combine old and new photos
            $validated['photos'] = array_merge($oldPhotos, $newPhotoPaths);
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
        $photos = $report->photos ?? [];
        if (!empty($photos)) {
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
            'photos' => $photoPaths // Langsung array
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
     * Auditor reject report - Status kembali ke in_progress untuk diperbaiki
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
        
        // Update status kembali ke 'in_progress' (bukan rejected)
        $report->status = 'in_progress';
        $report->rejection_reason = $validated['rejection_reason'];
        $report->approved_at = null;
        $report->fixed_at = null; // Reset fixed_at karena perlu diperbaiki lagi
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('warning', 'Report rejected and returned to In Progress status. Department needs to fix and resubmit.');
    }

    /**
     * Show export page with period options
     */
    public function exportPage()
    {
        // Hanya auditor dan super_admin
        if (!in_array(auth()->user()->role, ['auditor', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }
        
        // Ambil data tahun yang tersedia
        $availableYears = Report::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        // Hitung jumlah report per periode
        $reportStats = [
            'years' => [],
            'months' => []
        ];
        
        foreach ($availableYears as $year) {
            // Stats per tahun
            $yearCount = Report::whereYear('created_at', $year)->count();
            $reportStats['years'][$year] = $yearCount;
            
            // Stats per bulan dalam tahun tersebut
            for ($month = 1; $month <= 12; $month++) {
                $monthCount = Report::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
                
                if ($monthCount > 0) {
                    $reportStats['months'][$year][$month] = $monthCount;
                }
            }
        }
        
        return view('reports.export', compact('availableYears', 'reportStats'));
    }

    /**
     * Export and delete reports by period
     */
    public function exportAndDeleteByPeriod(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'period_type' => 'required|in:month,year',
            'year' => 'required|numeric|min:2020|max:' . date('Y'),
            'month' => 'required_if:period_type,month|nullable|numeric|min:1|max:12',
            'confirm_delete' => 'required|accepted',
        ], [
            'period_type.required' => 'Period type must be selected.',
            'year.required' => 'Year must be selected.',
            'month.required_if' => 'Month must be selected when choosing monthly period.',
            'confirm_delete.accepted' => 'You must confirm the deletion.',
        ]);
        
        $periodType = $validated['period_type'];
        $year = $validated['year'];
        $month = $validated['month'] ?? null;
        
        // Query berdasarkan periode
        $query = Report::with(['responses']);
        
        if ($periodType === 'month') {
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
            $periodLabel = date('F Y', mktime(0, 0, 0, $month, 1, $year));
            $filename = "Reports_" . $year . "_" . str_pad($month, 2, '0', STR_PAD_LEFT) . "_ARCHIVED_" . date('Ymd_His') . ".xlsx";
        } else {
            $query->whereYear('created_at', $year);
            $periodLabel = "Year {$year}";
            $filename = "Reports_{$year}_ARCHIVED_" . date('Ymd_His') . ".xlsx";
        }
        
        // Ambil reports
        $reports = $query->get();
        
        if ($reports->isEmpty()) {
            return redirect()->route('reports.exportPage')
                ->with('error', "No reports found for {$periodLabel}.");
        }
        
        $reportCount = $reports->count();
        
        try {
            DB::beginTransaction();
            
            // Export to Excel first
            $export = Excel::download(
                new ReportsExport($year, $month), 
                $filename
            );
            
            // Delete photos from storage
            foreach ($reports as $report) {
                // Delete report photos
                $photos = $report->photos ?? [];
                if (!empty($photos)) {
                    foreach ($photos as $photo) {
                        Storage::disk('public')->delete($photo);
                    }
                }
                
                // Delete response photos
                foreach ($report->responses as $response) {
                    $responsePhotos = $response->photos ?? [];
                    if (!empty($responsePhotos)) {
                        foreach ($responsePhotos as $photo) {
                            Storage::disk('public')->delete($photo);
                        }
                    }
                }
            }
            
            // Delete all responses first (foreign key constraint)
            ReportResponse::whereIn('report_id', $reports->pluck('id'))->delete();
            
            // Delete all reports
            Report::whereIn('id', $reports->pluck('id'))->delete();
            
            DB::commit();
            
            // Log the action
            Log::info("Reports archived and deleted", [
                'period_type' => $periodType,
                'period' => $periodLabel,
                'count' => $reportCount,
                'user' => auth()->user()->name,
                'user_role' => auth()->user()->role,
                'timestamp' => now(),
            ]);
            
            session()->flash('success', "Successfully exported and deleted {$reportCount} reports from {$periodLabel}.");
            
            return $export;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to export and delete reports", [
                'period' => $periodLabel,
                'error' => $e->getMessage(),
                'user' => auth()->user()->name,
            ]);
            
            return redirect()->route('reports.exportPage')
                ->with('error', 'Failed to export and delete reports. Please try again.');
        }
    }
}