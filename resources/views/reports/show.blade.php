@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">{{ $report->report_number }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h3 class="mb-2">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                                {{ $report->report_number }}
                            </h3>
                            <div class="mb-2">
                                {!! $report->status_badge !!}
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar3"></i>
                                Created: {{ $report->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            @if(Auth::user()->role === 'auditor' && Auth::user()->id === $report->auditor_id)
                                <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rejection Alert --}}
    @if($report->rejection_reason)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading fw-bold mb-2">
                                <i class="bi bi-x-octagon"></i> Report Rejected - Revision Required
                            </h5>
                            <p class="mb-2">
                                This report has been reviewed and requires corrections before it can be approved.
                                The status has been changed back to <strong>In Progress</strong>.
                            </p>
                            <hr class="my-3">
                            <div class="bg-white bg-opacity-50 p-3 rounded">
                                <p class="mb-1 fw-semibold text-dark">
                                    <i class="bi bi-chat-left-quote"></i> Rejection Reason:
                                </p>
                                <p class="mb-0 fst-italic text-dark">
                                    "{{ $report->rejection_reason }}"
                                </p>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <strong>Action Required:</strong> Please review the feedback above, make necessary corrections, and resubmit the report for review.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Warning Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="row g-4">
        {{-- Report Details --}}
        <div class="col-lg-8">
            {{-- Basic Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-primary"></i> Report Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold mb-1">Department</label>
                            <p class="mb-0">
                                <i class="bi bi-building text-primary"></i>
                                {{ $report->department->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold mb-1">Audit Type</label>
                            <p class="mb-0">
                                <i class="bi bi-clipboard-check text-info"></i>
                                {{ $report->auditType->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold mb-1">Auditor</label>
                            <p class="mb-0">
                                <i class="bi bi-person-badge text-success"></i>
                                {{ $report->auditor->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold mb-1">Location</label>
                            <p class="mb-0">
                                <i class="bi bi-geo-alt text-danger"></i>
                                {{ $report->location }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-semibold mb-1">Issue Type</label>
                            <p class="mb-0">
                                <span class="badge bg-warning">{{ $report->issue_type }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-card-text text-primary"></i> Description
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.8;">{{ $report->description }}</p>
                </div>
            </div>

            {{-- Photos --}}
            <div class="card border-0 shadow-sm mb-4">
                @php
                    $reportPhotos = is_array($report->photos) ? $report->photos : (json_decode($report->photos, true) ?? []);
                @endphp
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-images text-primary"></i> Evidence Photos ({{ count($reportPhotos) }})
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(count($reportPhotos) > 0)
                        <div class="row g-3">
                            @foreach($reportPhotos as $photo)
                                <div class="col-md-4">
                                    <a href="{{ Storage::url($photo) }}" data-lightbox="report-photos" data-title="Report Photo">
                                        <div class="ratio ratio-1x1">
                                            <img src="{{ Storage::url($photo) }}" 
                                                 class="img-fluid rounded shadow-sm object-fit-cover" 
                                                 alt="Report Photo"
                                                 style="cursor: pointer; transition: transform 0.2s;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'">
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No photos available</p>
                    @endif
                </div>
            </div>

            {{-- Responses --}}
            @if($report->responses->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-chat-left-dots text-primary"></i> Department Responses ({{ $report->responses->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach($report->responses as $response)
                            <div class="border-start border-primary border-4 bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-primary">{{ $response->user->name }}</strong>
                                        <small class="text-muted d-block">{{ $response->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                                <p class="mb-2" style="white-space: pre-line;">{{ $response->description }}</p>
                                
                                @php
                                    $responsePhotos = is_array($response->photos) ? $response->photos : (json_decode($response->photos, true) ?? []);
                                @endphp
                                
                                @if(count($responsePhotos) > 0)
                                    <div class="row g-2 mt-3">
                                        @foreach($responsePhotos as $photo)
                                            <div class="col-md-3">
                                                <a href="{{ Storage::url($photo) }}" data-lightbox="response-photos" data-title="Response Photo">
                                                    <div class="ratio ratio-1x1">
                                                        <img src="{{ Storage::url($photo) }}" 
                                                             class="img-fluid rounded shadow-sm object-fit-cover" 
                                                             alt="Response Photo"
                                                             style="cursor: pointer;">
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Timeline --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary"></i> Timeline
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        {{-- Submitted --}}
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                        <i class="bi bi-send text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <strong class="d-block">Submitted</strong>
                                    <small class="text-muted">{{ $report->submitted_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- In Progress --}}
                        @if($report->started_at)
                            <div class="timeline-item mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                            <i class="bi bi-hourglass-split text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <strong class="d-block">Started Progress</strong>
                                        <small class="text-muted">{{ $report->started_at->format('d M Y, H:i') }}</small>
                                        @if($report->deadline)
                                            <div class="mt-1">
                                                <span class="badge bg-danger">
                                                    Deadline: {{ $report->deadline->format('d M Y') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Fixed --}}
                        @if($report->fixed_at)
                            <div class="timeline-item mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                            <i class="bi bi-check-circle text-info"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <strong class="d-block">Fixed</strong>
                                        <small class="text-muted">{{ $report->fixed_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Approved --}}
                        @if($report->approved_at)
                            <div class="timeline-item">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                            <i class="bi bi-patch-check text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <strong class="d-block">Approved</strong>
                                        <small class="text-muted">{{ $report->approved_at->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-gear text-primary"></i> Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- Staff Department Actions --}}
                    @if(Auth::user()->role === 'staff_departemen' && Auth::user()->department_id === $report->department_id)
                        @if($report->status === 'submitted')
                            {{-- Start Progress --}}
                            <button class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#startProgressModal">
                                <i class="bi bi-play-circle"></i> Start Progress
                            </button>
                        @elseif($report->status === 'in_progress' && !$report->fixed_at)
                            {{-- Submit Response --}}
                            <button class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#respondModal">
                                <i class="bi bi-chat-left-text"></i> Submit Response
                            </button>
                        @endif
                    @endif

                    {{-- Auditor Actions --}}
                    @if(Auth::user()->role === 'auditor' && Auth::user()->id === $report->auditor_id)
                        @if($report->status === 'fixed')
                            {{-- Approve --}}
                            <form action="{{ route('reports.approve', $report) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to approve this report?')">
                                    <i class="bi bi-check-circle"></i> Approve Report
                                </button>
                            </form>
                            {{-- Reject --}}
                            <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject Report
                            </button>
                        @endif
                    @endif

                    @if($report->status === 'approved')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>Report Approved</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('reports.modals.start-progress')
@include('reports.modals.respond')
@include('reports.modals.reject')

{{-- Lightbox for images --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 35px;
    bottom: -15px;
    width: 2px;
    background: #e0e0e0;
}

.object-fit-cover {
    object-fit: cover;
}
</style>
@endsection