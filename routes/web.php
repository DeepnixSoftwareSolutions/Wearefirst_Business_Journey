<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Agent\StudentRegistrationController;
use App\Http\Controllers\Accountant\ApprovalController;
use App\Http\Controllers\Manager\PromotionController;
use App\Http\Controllers\Accountant\PayoutController;
use App\Http\Controllers\Manager\PayoutApprovalController;
use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\NetworkTreeController;
use App\Http\Controllers\Agent\GroupServiceController;
use App\Http\Controllers\Agent\MyNetworkController;
use App\Http\Controllers\Finance\PaymentReportController;
use App\Http\Controllers\Agent\LedgerController;
use App\Http\Controllers\Finance\CompanyLedgerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ----------------------------------------------------
// Public Routes
// ----------------------------------------------------
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ----------------------------------------------------
// Global Authenticated Routes (All Users including Students)
// ----------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/welcome-student', function () {
        return Inertia::render('Student/Welcome');
    })->name('student.welcome');
});

// ----------------------------------------------------
// Core Business Operations (All Staff / No Students)
// ----------------------------------------------------
Route::middleware(['auth', 'verified', 'role:Agent,Accountant,Manager,Admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Profile Management (Universal)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Unified Payment Submission & Viewing (Available to all staff)
    Route::get('/payments/due', [PaymentReportController::class, 'index'])->name('payments.due');
    Route::post('/payments/submit/{student}', [PaymentReportController::class, 'submitPaymentDetails'])->name('payments.submit');
});

// ----------------------------------------------------
// Agent Operations (Agents & Admins)
// ----------------------------------------------------
Route::middleware(['auth', 'role:Agent,Admin'])->group(function () {
    Route::get('/agent/group-service', [GroupServiceController::class, 'index'])->name('agent.group-service');
    Route::get('/agent/register-student', [StudentRegistrationController::class, 'create'])->name('agent.register');
    Route::post('/agent/register-student', [StudentRegistrationController::class, 'store']);
    Route::get('/agent/pending-registrations', [StudentRegistrationController::class, 'index'])->name('agent.pending');
    Route::put('/agent/pending-registrations/{student}', [StudentRegistrationController::class, 'update'])->name('agent.student.update');
    // Agent's Restricted Network List & Ledger
    Route::get('/agent/my-network', [MyNetworkController::class, 'index'])->name('agent.network');
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger');
});

// ----------------------------------------------------
// Manager Operations (Managers & Admins)
// ----------------------------------------------------
Route::middleware(['auth', 'role:Manager,Admin'])->group(function () {
    // Network Tree & API (Accountants/Agents cannot access this)
    Route::get('/network-tree', [NetworkTreeController::class, 'index'])->name('network.tree');
    Route::get('/api/network-tree', [NetworkTreeController::class, 'getTreeData']);
    Route::get('/manager/promotions', [PromotionController::class, 'index'])->name('manager.promotions');
    Route::post('/manager/promote/{student}', [PromotionController::class, 'promoteToAgent']);
    Route::get('/manager/pending-payouts', [PayoutApprovalController::class, 'getPendingRequests'])->name('manager.pending');
    Route::post('/manager/approve-payouts', [PayoutApprovalController::class, 'approveRequests']);
    Route::post('/manager/reject-payouts', [PayoutApprovalController::class, 'rejectRequests']);
});

// ----------------------------------------------------
// Accountant Operations (Accountants & Admins)
// ----------------------------------------------------
Route::middleware(['auth', 'role:Accountant,Admin,Manager'])->group(function () {
    Route::get('/accountant/pending-approvals', [ApprovalController::class, 'index'])->name('accountant.pending');
    Route::post('/accountant/approve-student/{student}', [ApprovalController::class, 'approveRegistration']);
    Route::post('/accountant/mark-remainder-paid/{student}', [ApprovalController::class, 'markRemainderPaid']);
    // Generic Reject route handles both initial and balance rejections
    Route::post('/accountant/reject-student/{student}', [ApprovalController::class, 'rejectRegistration']);
    Route::delete('/accountant/void-registration/{student}', [ApprovalController::class, 'voidRegistration']);
    // Payout Workflow
    Route::get('/accountant/eligible-payouts', [PayoutController::class, 'getEligibleAgents'])->name('accountant.payouts');
    Route::post('/accountant/submit-payouts', [PayoutController::class, 'submitRequests']);
    Route::get('/accountant/disburse-payments', [PayoutController::class, 'getApprovedPayouts'])->name('accountant.disburse');
    Route::post('/accountant/mark-payouts-paid', [PayoutController::class, 'markAsPaid']);
    // Company Ledger
    Route::get('/company-ledger', [CompanyLedgerController::class, 'index'])->name('company.ledger');
});

// ----------------------------------------------------
// Admin Exclusive Operations
// ----------------------------------------------------
Route::middleware(['auth', 'role:Admin,Manager'])->group(function () {
    Route::get('/admin/users', [AccountManagementController::class, 'index'])->name('admin.users');
    Route::post('/admin/agents/{agent}/toggle-hold', [AccountManagementController::class, 'toggleHoldStatus']);
    Route::post('/admin/agents/{agent}/unlock-legs', [AccountManagementController::class, 'unlockMiddleLegs'])->name('admin.agents.unlock-legs');
    Route::delete('/admin/users/{user}/safe-delete', [AccountManagementController::class, 'safeDeleteUser']);
    Route::post('/admin/users/{user}/award-offer', [AccountManagementController::class, 'awardOffer']);
    Route::post('/admin/users/{user}/update-master-profile', [AccountManagementController::class, 'updateMasterProfile'])->name('admin.users.update-profile');
});

require __DIR__.'/auth.php';