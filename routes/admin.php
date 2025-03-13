<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\SavingsController;
use App\Http\Controllers\Admin\SettingController;
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
    Route::post('/users/bank/{user}', [UserController::class, 'bank'])->name('users.bank');
    Route::delete('/users/delete/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::post('/user/credit/{user}', [UserController::class, 'credit'])->name('user.credit');
    Route::post('/user/debit/{user}', [UserController::class, 'debit'])->name('user.debit');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions/store', [TransactionController::class, 'addTransaction'])->name('transactions.store');
    Route::post('/transactions/deposit/{transaction}', [TransactionController::class, 'deposit'])->name('transactions.deposit');
    Route::post('/transactions/withdraw/{transaction}', [TransactionController::class, 'withdraw'])->name('transactions.withdraw');
    Route::post('/transactions/{transactions}/decline', [TransactionController::class, 'decline'])->name('transactions.decline');
    Route::delete('/transactions/{transaction}/destroy', [TransactionController::class, 'destroyTransaction'])->name('transactions.destroy');

    Route::get('/trades', [UserController::class, 'trades'])->name('trades');
    Route::post('/trades/user/create', [TradeController::class, 'store'])->name('trade.create');
    Route::put('/trades/user/update/{trade}', [TradeController::class, 'update'])->name('trade.update');
    Route::put('/trades/user/toggle/{trade}', [TradeController::class, 'toggle'])->name('trade.toggle');
    Route::delete('/trades/destroy/{trade}', [TradeController::class, 'destroy'])->name('trade.destroy');

    Route::get('/savings', [SavingsController::class, 'index'])->name('account.savings');
    Route::get('/savings-accounts', [SavingsController::class, 'accounts'])->name('accounts.savings');
    Route::post('/savings-account/store', [SavingsController::class, 'storeAccounts'])->name('account.savings.store');
    Route::put('/savings-account/update/{savingsAccount}', [SavingsController::class, 'updateAccounts'])->name('account.savings.update');
    Route::delete('/savings-account/destroy/{savingsAccount}', [SavingsController::class, 'destroyAccount'])->name('account.savings.destroy');
    Route::get('/savings/{user}/transactions/{savings}', [SavingsController::class, 'transactions'])->name('accounts.transactions');
    Route::post('/savings/{user}/contribue/{savings}', [SavingsController::class, 'contribute'])->name('accounts.contribute');
    Route::delete('/savings/destroy/{savingsLedger}', [SavingsController::class, 'destroy'])->name('savings.destroy');
    Route::post('/savings/user/account', [SavingsController::class, 'store'])->name('account.store');
    Route::get('/savings/transactions', [SavingsController::class, 'fetchTransactions'])->name('account.fetch.transactions');


    Route::get('/articles/all', [ArticleController::class, 'index'])->name('article.all');
    Route::post('/articles/store', [ArticleController::class, 'store'])->name('article.store');
    Route::put('/articles/edit/{article}', [ArticleController::class, 'update'])->name('article.edit');
    Route::put('/articles/toggle/{article}', [ArticleController::class, 'toggle'])->name('article.toggle');
    Route::delete('/articles/destroy/{article}', [ArticleController::class, 'destroy'])->name('article.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/positions', [PositionController::class, 'index'])->name('positions');
    Route::post('/positions/user/create', [PositionController::class, 'store'])->name('position.create');
    Route::post('/positions/user/close', [PositionController::class, 'close'])->name('position.close');
    // Route::put('/trades/user/toggle/{trade}', [TradeController::class, 'toggle'])->name('trade.toggle');
    // Route::delete('/trades/destroy/{trade}', [TradeController::class, 'destroy'])->name('trade.destroy');

});