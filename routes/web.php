<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\BillingSummaryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\StatementOfAccountController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\InvoiceController;

// Public
Route::get('/', function () {
    return view('welcome');
});

// Redirect based on role after login
Route::get('/dashboard', function () {
    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'billing_clerk' => redirect()->route('billing.dashboard'),
        'receivable_clerk' => redirect()->route('receivable.dashboard'),
        default => abort(403, 'Unauthorized'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ---------------- ADMIN ROUTES ----------------
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

     Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');


     Route::get('billing', [BillingController::class, 'index'])->name('billing');
    Route::get('billing/clients', [BillingController::class, 'clients'])->name('billing.clients');
    Route::get('billing/departments', [BillingController::class, 'departments'])->name('billing.departments');
    Route::get('billing/summaries', [BillingController::class, 'getBillingSummaries'])->name('billing.summaries');
    Route::post('billing/store', [BillingController::class, 'store'])->name('billing.store');


    //route for billing-summary.blade.php
    //Billing Summary
    Route::get('/billing-summary', function () {return view('admin.billing-summary');})->name('billing-summary');
    // Billing Summary Save (POST)
    Route::post('/billing-summary/save', [BillingSummaryController::class, 'store'])
    ->name('billing-summary.save');

    //route for invoice.blade.php

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/clients', [InvoiceController::class, 'getClients'])->name('invoice.clients');
    Route::get('/invoice/departments/{clientId}', [InvoiceController::class, 'getDepartments'])->name('invoice.departments');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/next-number', [InvoiceController::class, 'nextInvoiceNumber'])->name('invoice.nextInvoiceNumber');

    //route for records.blade.php
    Route::get('/records', function () {
        return view('admin.records');
    })->name('records');

      // Receivables

        Route::get('/receive-payment', function () {
            return view('admin.receive-payment');
        })->name('receive-payment');

        Route::get('/receivable-records', function () {
            return view('admin.receivable-records');
        })->name('receivable-records');


    // Settings / Users

//route for profile-settings.blade.php
Route::get('/auth/profile-settings', function () {
    $user = Auth::user(); // get currently logged-in user
    return view('admin.auth.profile-settings', compact('user'));
})->name('profile-settings');

    //route for system-users.blade.php
    //MANAGE
    Route::get('/system-users', function () {
        return view('admin.system-users');
    })->name('system.users');
    Route::get('/system-users', function () {
    return view('admin.system-users');
    })->name('system-users');


    //route for change-password.blade.php
    // Admin Register New User
    Route::get('/register-user', [AdminRegisterController::class, 'create'])
    ->name('register-user');
    Route::post('/register-user', [AdminRegisterController::class, 'store'])
        ->name('register-user.store');





});



// ---------------- BILLING CLERK ----------------
Route::middleware(['auth'])->group(function () {
    Route::get('/billing/dashboard', function () {
        return view('billing.dashboard');
    })->name('billing.dashboard');
});




// ---------------- RECEIVABLE CLERK ----------------
Route::middleware(['auth'])->group(function () {
    Route::get('/receivable/dashboard', function () {
        return view('receivable.dashboard');
    })->name('receivable.dashboard');
});



// ---------------- PROFILE ----------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
