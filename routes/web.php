<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Reports - Resource Routes (sudah include index, create, store, show, edit, update, destroy)
    // Tapi kita custom middleware-nya
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create')->middleware('role:auditor');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store')->middleware('role:auditor');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit')->middleware('role:auditor');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update')->middleware('role:auditor');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy')->middleware('role:auditor');
    
    // Department Staff Actions
    Route::post('/reports/{report}/start-progress', [ReportController::class, 'startProgress'])
        ->name('reports.start-progress')
        ->middleware('role:staff_departemen');
    Route::post('/reports/{report}/respond', [ReportController::class, 'respond'])
        ->name('reports.respond')
        ->middleware('role:staff_departemen');
    
    // Auditor Actions (Approve/Reject hasil perbaikan dari departemen)
    Route::post('/reports/{report}/approve', [ReportController::class, 'approve'])
        ->name('reports.approve')
        ->middleware('role:auditor');
    Route::post('/reports/{report}/reject', [ReportController::class, 'reject'])
        ->name('reports.reject')
        ->middleware('role:auditor');
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Departments
        Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
        Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
        Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{department}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');
        
        // Audit Types
        Route::get('/audit-types', [AdminController::class, 'auditTypes'])->name('audit-types');
        Route::post('/audit-types', [AdminController::class, 'storeAuditType'])->name('audit-types.store');
        Route::put('/audit-types/{auditType}', [AdminController::class, 'updateAuditType'])->name('audit-types.update');
        Route::delete('/audit-types/{auditType}', [AdminController::class, 'deleteAuditType'])->name('audit-types.delete');
        
        // Users
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    });
});