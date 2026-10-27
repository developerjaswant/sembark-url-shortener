<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ClientController as SuperAdminClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ShortUrlController as AdminShortUrlController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ShortUrlController as MemberShortUrlController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'SuperAdmin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($role === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'Member') {
            return redirect()->route('member.dashboard');
        }
    }

    return redirect()->route('login');
});

Route::middleware(['auth', 'role:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/export', [SuperAdminDashboardController::class, 'export'])->name('export');
    Route::any('/clients/add', [SuperAdminClientController::class, 'index'])->name('clients.add');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/export', [AdminDashboardController::class, 'export'])->name('export');
    Route::post('/short-urls/add', [AdminShortUrlController::class, 'index'])->name('short_urls.add');
    Route::post('/members/add', [AdminMemberController::class, 'index'])->name('members.add');
});

Route::middleware(['auth', 'role:Member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/export', [MemberDashboardController::class, 'export'])->name('export');
    Route::any('/short-urls/add', [MemberShortUrlController::class, 'index'])->name('short_urls.add');
});

Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');

Route::get('/links/{code}', [ShortUrlController::class, 'redirect'])->name('short_urls.redirect');

require __DIR__ . '/auth.php';
