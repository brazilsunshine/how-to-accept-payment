<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * SUBSCRIBE
 */
Route::middleware(['auth:sanctum', 'verified', 'nonPayingCustomer'])->get('/subscribe', function () {
    return view('/subscribe', [
        'intent' => auth()->user()->createSetupIntent(),
    ]);
})->name('subscribe');

Route::middleware(['auth:sanctum', 'verified', 'nonPayingCustomer'])->post('/subscribe', function (Request $request) {
    //dd($request->all());
    auth()->user()->newSubscription('cashier', $request->plan)->create($request->paymentMethod);

    return redirect('/dashboard');
})->name('subscribe.post');

/**
 * MEMBERS
 */
Route::middleware(['auth:sanctum', 'verified', 'payingCustomer'])->get('/members', function () {
    return view('members');
})->name('members');


/**
 * SINGLE CHARGE
 */
Route::middleware(['auth:sanctum', 'verified'])->get('/charge', function () {
    return view('charge');
})->name('charge');

Route::middleware(['auth:sanctum', 'verified'])->post('/charge', function (Request $request) {
     //dd($request->all());
    auth()->user()->charge(1000, $request->paymentMethod);

    return redirect('/dashboard');
})->name('charge.post');

/**
 * GENERATE INVOICES
 */
Route::middleware(['auth:sanctum', 'verified'])->post('/charge', function (Request $request) {
    //dd($request->all());
    auth()->user()->createAsStripeCustomer();
    auth()->user()->updateDefaultPaymentMethod($request->paymentMethod);
    auth()->user()->invoiceFor('product name here', 1500);

    return redirect('/dashboard');
})->name('charge.post');

Route::middleware(['auth:sanctum', 'verified'])->get('/invoices', function () {
    return view('invoices', [
        'invoices' => auth()->user()->invoices(),
    ]);
})->name('invoices');

Route::get('user/invoice/{invoice}', function (Request $request, $invoiceId) {
    return $request->user()->downloadInvoice($invoiceId, [
        'vendor' => 'Your company',
        'product' => 'your product',
    ]);
});


require __DIR__.'/auth.php';
