<?php

use App\Models\transaction as ModelsTransaction;
use Illuminate\Support\Facades\Route;
use Tzsk\Payu\Concerns\Attributes;
use Tzsk\Payu\Concerns\Customer;
use Tzsk\Payu\Concerns\Transaction;
use Tzsk\Payu\Facades\Payu;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('payment', function(){
    
    $customer = Customer::make()
    ->firstName('John Doe')
    ->email('john@example.com');

    // This is entirely optional custom attributes
    $attributes = Attributes::make()
    ->udf1('anything');

    $transaction = Transaction::make()
    ->charge(100)
    ->for('Product')
    ->with($attributes) // Only when using any custom attributes
    ->to($customer);

    ModelsTransaction::create([
        'txn' => $transaction->transactionId,
    ]);
    return Payu::initiate($transaction)->redirect(route('status'));
});

Route::get('status', function(){
    $transaction = Payu::capture();
    if($transaction->status == 'successful'){
        $original_txn = ModelsTransaction::where('txn', $transaction->transaction_id)->first();
        return $original_txn;
    }
    else{
        return 'failled';
    }
})->name('status');