<?php

use App\Http\Controllers\TicketingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketingController::class, 'index'])->name('home');

Route::get('/event/{event}/checkout', [TicketingController::class, 'showCheckout'])->name('checkout.show');
Route::post('/event/{event}/checkout', [TicketingController::class, 'checkout'])->name('checkout.store');

Route::get('/booking/{order_id}/payment', [TicketingController::class, 'showPayment'])->name('booking.payment');
Route::post('/api/midtrans/webhook', [TicketingController::class, 'webhook'])->name('booking.webhook');

Route::get('/booking/{order_id}/success', [TicketingController::class, 'showSuccess'])->name('booking.success');
Route::get('/booking/{order_id}/pending', [TicketingController::class, 'showPending'])->name('booking.pending');
Route::get('/booking/{order_id}/failed', [TicketingController::class, 'showFailed'])->name('booking.failed');
