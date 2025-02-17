<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SavingsController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;


Route::get('/login', function () {
    return redirect('admin.login');
})->name('login');

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected admin routes
Route::group(['middleware' => ['active_admin']], function (){
    Route::get('/alt/login', [UserController::class, 'showLogin'])->name('altLogin');
    Route::post('/alt/login', [UserController::class, 'login'])->name('altLogin');  

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/update/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/toggle/{user}', [UserController::class, 'toggle'])->name('users.toggle');

    Route::post('/user/credit/{user}', [UserController::class, 'credit'])->name('user.credit');
    Route::post('/user/debit/{user}', [UserController::class, 'debit'])->name('user.debit');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions/{transaction}/deposit', [TransactionController::class, 'deposit'])->name('transactions.deposit');
    Route::post('/transactions/{transactions}/decline', [TransactionController::class, 'decline'])->name('transactions.decline');

    Route::get('/trades', [UserController::class, 'trades'])->name('trades');
    Route::post('/trades/user/create', [TradeController::class, 'store'])->name('trade.create');
    Route::put('/trades/user/update/{trade}', [TradeController::class, 'update'])->name('trade.update');
    Route::put('/trades/user/toggle/{trade}', [TradeController::class, 'toggle'])->name('trade.toggle');

    Route::get('/savings', [SavingsController::class, 'index'])->name('account.savings');
    Route::get('/savings-accounts', [SavingsController::class, 'accounts'])->name('accounts.savings');
});