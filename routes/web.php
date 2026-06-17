<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\PermitIssuanceController;
use App\Http\Controllers\PermitRenewalController;
use App\Http\Controllers\PermitVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
})->name('home');

/** Public verification — registered before permits resource so `{permit}` does not capture `verify`. */
Route::get('/permits/verify/{code?}', [PermitVerificationController::class, 'show'])
    ->name('permits.verify');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    Route::get('archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('archives/permits', [ArchiveController::class, 'permits'])->name('archives.permits');
    Route::get('archives/drivers', [ArchiveController::class, 'drivers'])->name('archives.drivers');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/signature', [SettingsController::class, 'updateSignature'])->name('settings.signature.update');
    Route::delete('settings/signature', [SettingsController::class, 'destroySignature'])->name('settings.signature.destroy');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('drivers', DriverController::class);
    Route::post('drivers/{driver}/archive', [DriverController::class, 'archive'])->name('drivers.archive');
    Route::post('drivers/{driver}/restore', [DriverController::class, 'restore'])->name('drivers.restore');

    Route::get('permits/renewals', [PermitRenewalController::class, 'index'])->name('permits.renewals.index');

    Route::get('permits/issue', [PermitIssuanceController::class, 'showDriver'])->name('permits.issue');
    Route::post('permits/issue/driver', [PermitIssuanceController::class, 'storeDriver'])->name('permits.issue.driver');
    Route::get('permits/issue/validity', [PermitIssuanceController::class, 'showValidity'])->name('permits.issue.validity');
    Route::post('permits/issue/validity', [PermitIssuanceController::class, 'storeValidity'])->name('permits.issue.validity.store');
    Route::get('permits/issue/review', [PermitIssuanceController::class, 'showReview'])->name('permits.issue.review');
    Route::post('permits/issue/review', [PermitIssuanceController::class, 'storeReview'])->name('permits.issue.review.store');
    Route::get('permits/issue/generate', [PermitIssuanceController::class, 'showGenerate'])->name('permits.issue.generate');
    Route::post('permits/issue/generate', [PermitIssuanceController::class, 'storeGenerate'])->name('permits.issue.generate.store');
    Route::get('permits/issue/complete/{permit}', [PermitIssuanceController::class, 'complete'])->name('permits.issue.complete');
    Route::post('permits/issue/cancel', [PermitIssuanceController::class, 'cancel'])->name('permits.issue.cancel');

    Route::get('permits/{permit}/certificate', [PermitController::class, 'certificate'])->name('permits.certificate');
    Route::resource('permits', PermitController::class);
    Route::post('permits/{permit}/revoke', [PermitController::class, 'revoke'])->name('permits.revoke');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
