<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ReconciliationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\TransactionApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AdministrativeTransactionController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CaseDocumentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceCaseController;
use App\Http\Controllers\SlaMonitoringController;
use App\Http\Controllers\TransactionCorrectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public authentication routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated application routes
|--------------------------------------------------------------------------
| IMPORTANT: create/static URLs are declared before parameter URLs so paths
| such as /customers/create and /cases/create never resolve as an ID.
*/
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    // Compatibility aliases for common Indonesian links / old bookmarks.
    Route::redirect('/nasabah', '/customers')->name('legacy.customers.index');
    Route::redirect('/berkas', '/cases')->name('legacy.cases.index');
    Route::redirect('/monitoring', '/monitoring-sla')->name('legacy.sla.index');
    Route::redirect('/arsip', '/archives')->name('legacy.archives.index');

    // Shared READ routes: Admin sees all data, CS sees assigned data only.
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/cases', [ServiceCaseController::class, 'index'])->name('cases.index');
    Route::get('/transactions', [AdministrativeTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/monitoring-sla', [SlaMonitoringController::class, 'index'])->name('sla.index');
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/documents/{document}/download', [CaseDocumentController::class, 'download'])->name('documents.download');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Maker / Customer Service operational CRUD.
    Route::middleware('role:cs')->group(function (): void {
        // Customer CRUD: safe delete is represented by Admin deactivation.
        Route::get('/nasabah/tambah', fn () => redirect()->route('customers.create'))->name('legacy.customers.create');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::match(['put', 'patch'], '/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

        // Service case CRUD / lifecycle.
        Route::get('/berkas/tambah', fn () => redirect()->route('cases.create'))->name('legacy.cases.create');
        Route::get('/cases/create', [ServiceCaseController::class, 'create'])->name('cases.create');
        Route::post('/cases', [ServiceCaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{serviceCase}/edit', [ServiceCaseController::class, 'edit'])->name('cases.edit');
        Route::match(['put', 'patch'], '/cases/{serviceCase}', [ServiceCaseController::class, 'update'])->name('cases.update');
        Route::delete('/cases/{serviceCase}', [ServiceCaseController::class, 'destroy'])->name('cases.destroy');
        Route::post('/cases/{serviceCase}/process', [ServiceCaseController::class, 'process'])->name('cases.process');
        Route::post('/cases/{serviceCase}/complete', [ServiceCaseController::class, 'complete'])->name('cases.complete');
        Route::post('/cases/{serviceCase}/reject', [ServiceCaseController::class, 'reject'])->name('cases.reject');

        // Digital archive CRUD bound to its service case.
        Route::post('/cases/{serviceCase}/documents', [CaseDocumentController::class, 'store'])->name('cases.documents.store');
        Route::get('/documents/{document}/edit', [CaseDocumentController::class, 'edit'])->name('documents.edit');
        Route::match(['put', 'patch'], '/documents/{document}', [CaseDocumentController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{document}', [CaseDocumentController::class, 'destroy'])->name('documents.destroy');

        // Transaction CRUD. Financial rows are never physically deleted.
        Route::post('/cases/{serviceCase}/transactions', [AdministrativeTransactionController::class, 'store'])->name('cases.transactions.store');
        Route::get('/transactions/{transaction}/edit', [AdministrativeTransactionController::class, 'edit'])->name('transactions.edit');
        Route::match(['put', 'patch'], '/transactions/{transaction}', [AdministrativeTransactionController::class, 'update'])->name('transactions.update');
        Route::post('/transactions/{transaction}/submit', [AdministrativeTransactionController::class, 'submit'])->name('transactions.submit');
        Route::delete('/transactions/{transaction}', [AdministrativeTransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::get('/transactions/{transaction}/correction', [TransactionCorrectionController::class, 'create'])->name('transactions.corrections.create');
        Route::post('/transactions/{transaction}/correction', [TransactionCorrectionController::class, 'store'])->name('transactions.corrections.store');
    });

    // Dynamic detail routes are deliberately registered after static create URLs.
    // This prevents `/customers/create` and `/cases/create` being interpreted as an ID.
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/cases/{serviceCase}', [ServiceCaseController::class, 'show'])->name('cases.show');
    Route::get('/transactions/{transaction}', [AdministrativeTransactionController::class, 'show'])->name('transactions.show');

    // Checker / Admin controls, approval, master data and reporting.
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::match(['put', 'patch'], '/customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');

        Route::get('/transactions', [TransactionApprovalController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionApprovalController::class, 'show'])->name('transactions.show');
        Route::post('/transactions/{transaction}/verify', [TransactionApprovalController::class, 'verify'])->name('transactions.verify');

        Route::get('/corrections', [TransactionCorrectionController::class, 'index'])->name('corrections.index');
        Route::get('/corrections/{correction}', [TransactionCorrectionController::class, 'show'])->name('corrections.show');
        Route::post('/corrections/{correction}/verify', [TransactionCorrectionController::class, 'verify'])->name('corrections.verify');

        Route::get('/reconciliations', [ReconciliationController::class, 'index'])->name('reconciliations.index');
        Route::post('/reconciliations', [ReconciliationController::class, 'store'])->name('reconciliations.store');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/transactions/excel', [ReportController::class, 'downloadTransactionsExcel'])->name('reports.transactions.excel');
        Route::get('/reports/transactions/pdf', [ReportController::class, 'downloadTransactionsPdf'])->name('reports.transactions.pdf');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

        Route::resource('service-types', ServiceTypeController::class)->except(['show']);
        Route::match(['put', 'patch'], '/service-types/{serviceType}/status', [ServiceTypeController::class, 'updateStatus'])->name('service-types.status');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], '/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::match(['put', 'patch'], '/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.status');
    });
});

// Friendly fallback prevents a raw Laravel "Not Found" screen for stale links.
Route::fallback(function (Request $request) {
    if ($request->user()) {
        return response()->view('errors.404', [], 404);
    }

    return redirect()->route('login');
});
