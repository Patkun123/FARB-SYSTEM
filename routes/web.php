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
use App\Http\Controllers\Admin\SystemUserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReceivableController;
use App\Http\Controllers\Admin\InvoicePaymentController;
use App\Http\Controllers\Admin\PaymentRecordController;
use App\Http\Controllers\Admin\SummaryRecordController;
use App\Http\Controllers\Admin\InvoiceMailerController;





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

    //clients.blade.php
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    //billing.blade.php  routes
    Route::get('billing', [BillingController::class, 'index'])->name('billing');
    Route::get('billing/clients', [BillingController::class, 'clients'])->name('billing.clients');
    Route::get('billing/departments', [BillingController::class, 'departments'])->name('billing.departments');
    Route::get('billing/summaries', [BillingController::class, 'getBillingSummaries'])
    ->name('billing.getBillingSummaries');
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



Route::get('/summary-records', function () {
        return view('admin.summary-records');
    })->name('summary-records');
  Route::get('/summary-records/api', [SummaryRecordController::class, 'index'])
        ->name('summary-records.api');

    
    Route::get('/soa-record', function () {
        return view('admin.soa-record');
    })->name('soa-record');
     Route::get('/invoices', [InvoiceMailerController::class, 'index'])->name('admin.invoices.index');


// Routes for billing summaries
Route::prefix('billing')->group(function () {
    Route::get('/summaries', [SummaryRecordController::class, 'index'])->name('billing.index');
    Route::post('/summaries', [SummaryRecordController::class, 'store'])->name('billing.store');
    Route::get('/summaries/{id}', [SummaryRecordController::class, 'show'])->name('billing.show');
    Route::put('/summaries/{id}', [SummaryRecordController::class, 'update'])->name('billing.update');
    Route::delete('/summaries/{id}', [SummaryRecordController::class, 'destroy'])->name('billing.destroy');

    // Additional endpoints
    Route::get('/summaries/{id}/totals', [SummaryRecordController::class, 'getTotals'])->name('billing.totals');
    Route::get('/summaries/{id}/employees', [SummaryRecordController::class, 'getEmployees'])->name('billing.employees');
});
        //receive receive-payment.blade.php store code and load data 
   
   // Blade page
    Route::get('/receive-payment', function () {
    return view('admin.receive-payment');
    })->name('receive-payment');
Route::post('/receive-payment', [InvoicePaymentController::class, 'storePayment'])
    ->name('receive-payment.store');
Route::get('/receive-payment/api/invoices', [InvoicePaymentController::class, 'index'])
    ->name('receive-payment.api.invoices');
  
        //receivable records
    Route::get('/receivable-records', [PaymentRecordController::class, 'index'])->name('receivable-records');
    Route::get('/receivable-payments/{id}/edit', [PaymentRecordController::class, 'edit'])->name('receivable-payments.edit');
    Route::put('/receivable-payments/{id}', [PaymentRecordController::class, 'update'])->name('receivable-payments.update');
    Route::delete('/receivable-payments/{id}', [PaymentRecordController::class, 'destroy'])->name('receivable-payments.destroy');


    // Settings / Users

//route for profile-settings.blade.php
Route::get('/auth/profile-settings', function () {
    $user = Auth::user(); // get currently logged-in user
    return view('admin.auth.profile-settings', compact('user'));
})->name('profile-settings');

    //route for system-users.blade.php
    //MANAGE
// Display all system users
    Route::get('/system-users', [SystemUserController::class, 'index'])
        ->name('system.users');

    // Fetch a single user (for AJAX editing)
    Route::get('/system-users/{user}', [SystemUserController::class, 'show'])
        ->name('system.users.show');
    // Update a user
    Route::put('/system-users/{user}', [SystemUserController::class, 'update'])
        ->name('system.users.update');
    // Delete a user
    Route::delete('/system-users/{user}', [SystemUserController::class, 'destroy'])
        ->name('system.users.destroy');



    //route for change-password.blade.php
    // Admin Register New User
    Route::get('/register-user', [AdminRegisterController::class, 'create'])
    ->name('register-user');
    Route::post('/register-user', [AdminRegisterController::class, 'store'])
        ->name('register-user.store');





});



// ---------------- BILLING CLERK ---------------- view file is in resources/views/billing/
Route::middleware(['auth'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/dashboard', function () {
        return view('billing.dashboard');
    })->name('dashboard');

    Route::get('/auth/profile-settings', function () {
        $user = Auth::user();
        return view('billing.auth.profile-settings', compact('user'));
    })->name('profile-settings');
    
    
    //billing page billing/billing.blade.php
        Route::get('/billing', function () {
            return view('billing.billing');
        })->name('billing');

        Route::get('/billing', function () {
            return view('billing.invoice');
        })->name('invoice');


        Route::get('/billing-summary', function () {
            return view('billing.billing-summary');
        })->name('billing-summary');

        Route::post('/billing-summary/save', [BillingSummaryController::class, 'store'])
        ->name('billing-summary.save');
        


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