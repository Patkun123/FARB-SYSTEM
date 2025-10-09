<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\BillingSummaryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\AdminRegisterController;
use Illuminate\Support\Facades\Auth;

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

  // CLients Management - CRUD via Controller RESTful
    Route::resource('clients', ClientController::class)->except(['show']);

    // Manage Billing
    Route::get('/billing', function () {return view('admin.billing');})->name('billing');
    //ajax routes for clients and departments
    Route::get('/billing/clients', [BillingController::class, 'getClients'])->name('billing.clients');
    Route::get('/billing/departments', [BillingController::class, 'getDepartments'])->name('billing.departments');
    //billing summaries with totals
    Route::get('/billing/summaries', [BillingController::class, 'getBillingSummaries'])->name('billing.summaries');



    //Billing Summary
    Route::get('/billing-summary', function () {return view('admin.billing-summary');})->name('billing-summary');
    // Billing Summary Save (POST)
    Route::post('/billing-summary/save', [BillingSummaryController::class, 'store'])
    ->name('billing-summary.save');

    Route::get('/invoice', function () {
        return view('admin.invoice');
    })->name('invoice');

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


Route::get('/auth/profile-settings', function () {
    $user = Auth::user(); // get currently logged-in user
    return view('admin.auth.profile-settings', compact('user'));
})->name('profile-settings');


    //MANAGE
    Route::get('/system-users', function () {
        return view('admin.system-users');
    })->name('system.users');
    Route::get('/system-users', function () {
    return view('admin.system-users');
    })->name('system-users');

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
