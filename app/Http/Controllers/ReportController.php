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
    public function index()
    {
        $user = auth()->user();
        
        // Query berdasarkan role
        if ($user->role === 'super_admin') {
            $reports = Report::with(['department', 'auditType', 'auditor'])
                ->latest()
                ->paginate(15);
        } elseif ($user->role === 'staff_departemen') {
            $reports = Report::with(['department', 'auditType', 'auditor'])
                ->where('department_id', $user->department_id)
                ->latest()
                ->paginate(15);
        } else { // auditor
            $reports = Report::with(['department', 'auditType', 'auditor'])
                ->where('auditor_id', $user->id)
                ->latest()
                ->paginate(15);
        }
        
        // Kirim data departments dan auditTypes untuk filter
        $departments = \App\Models\Department::all();
        $auditTypes = \App\Models\AuditType::all();
        
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
            'department_id.required' => 'Departemen harus dipilih.',
            'location.required' => 'Lokasi harus diisi.',
            'issue_type.required' => 'Jenis masalah harus diisi.',
            'description.required' => 'Deskripsi harus diisi.',
            'photos.required' => 'Foto harus diupload.',
            'photos.min' => 'Minimal upload 1 foto.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photos.*.max' => 'Ukuran gambar maksimal 5MB.'
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
            ->with('success', 'Laporan berhasil dibuat.');
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
        
        return view('reports.edit', compact('report', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        // Upload new photos if provided
        if ($request->hasFile('photos')) {
            // Delete old photos
            $oldPhotos = json_decode($report->photos, true);
            if ($oldPhotos) {
                foreach ($oldPhotos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
            
            // Upload new photos
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reports', 'public');
                $photoPaths[] = $path;
            }
            $validated['photos'] = json_encode($photoPaths);
        }

        $report->update($validated);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Laporan berhasil diperbarui.');
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
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Mulai progress audit dengan deadline
     */
    public function startProgress($id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi input deadline
        $validated = request()->validate([
            'deadline' => 'required|date|after:today',
            'deadline_reason' => 'required|string|min:5'
        ], [
            'deadline.required' => 'Tanggal deadline harus diisi.',
            'deadline.date' => 'Format tanggal tidak valid.',
            'deadline.after' => 'Tanggal deadline harus setelah hari ini.',
            'deadline_reason.required' => 'Keterangan deadline harus diisi.',
            'deadline_reason.min' => 'Keterangan deadline minimal 5 karakter.'
        ]);

        // Update status dan deadline
        $report->status = 'in_progress';
        $report->started_at = now();
        $report->deadline = $validated['deadline'];
        $report->deadline_reason = $validated['deadline_reason'];
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('success', 'Progress laporan telah dimulai. Deadline: ' . \Carbon\Carbon::parse($validated['deadline'])->locale('id')->format('d F Y'));
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
            'description.required' => 'Deskripsi harus diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'photos.required' => 'Foto bukti perbaikan harus diupload.',
            'photos.min' => 'Minimal upload 1 foto.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photos.*.max' => 'Ukuran gambar maksimal 5MB.'
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
            ->with('success', 'Response berhasil dikirim. Status laporan diubah menjadi Fixed.');
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
     * Status changes back to 'in_progress' for revision
     */
    public function reject(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.'
        ]);
        
        // Update status to 'in_progress' (not 'rejected')
        // Rejected means needs revision, so it goes back to in_progress
        $report->status = 'in_progress';
        $report->rejection_reason = $validated['rejection_reason'];
        $report->approved_at = null;
        $report->fixed_at = null; // Reset fixed_at because it needs to be fixed again
        $report->save();
        
        return redirect()->route('reports.show', $id)
            ->with('warning', 'Report rejected and sent back to In Progress for revision. The department has been notified.');
    }
}