<?php

use App\Http\Controllers\TicketingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketingController::class, 'index'])->name('home');

Route::get('/event/{event}/checkout', [TicketingController::class, 'showCheckout'])->name('checkout.show');
Route::post('/event/{event}/checkout', [TicketingController::class, 'checkout'])->name('checkout.store');

Route::get('/booking/{invoice_number}/simulator', [TicketingController::class, 'showSimulator'])->name('booking.simulator');
Route::post('/booking/{invoice_number}/simulate-success', [TicketingController::class, 'simulateSuccess'])->name('booking.simulate_success');
Route::post('/booking/{invoice_number}/simulate-fail', [TicketingController::class, 'simulateFail'])->name('booking.simulate_fail');

Route::get('/booking/{invoice_number}/success', [TicketingController::class, 'showSuccess'])->name('booking.success');
Route::get('/booking/{invoice_number}/failed', [TicketingController::class, 'showFailed'])->name('booking.failed');
