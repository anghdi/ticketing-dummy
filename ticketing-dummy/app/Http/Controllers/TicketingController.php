<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketingController extends Controller
{
    /**
     * Inisialisasi konfigurasi Midtrans.
     */
    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$clientKey = config('midtrans.client_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Tampilkan katalog event.
     */
    public function index()
    {
        $events = Event::orderBy('date_time', 'asc')->get();
        return view('events.index', compact('events'));
    }

    /**
     * Tampilkan halaman checkout untuk event tertentu.
     */
    public function showCheckout(Event $event)
    {
        if ($event->quota <= 0) {
            return redirect()->route('home')->with('error', 'Maaf, tiket untuk event ini sudah habis terjual.');
        }

        return view('events.checkout', compact('event'));
    }

    /**
     * Proses pembuatan booking (status PENDING) dan minta snap_token ke Midtrans.
     */
    public function checkout(Request $request, Event $event)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|min:9|max:15',
            'ticket_qty' => 'required|integer|min:1|max:5',
        ], [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_email.required' => 'Alamat email wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_phone.required' => 'Nomor telepon wajib diisi.',
            'customer_phone.min' => 'Nomor telepon minimal 9 digit.',
            'ticket_qty.required' => 'Jumlah tiket wajib diisi.',
            'ticket_qty.min' => 'Minimal pembelian 1 tiket.',
            'ticket_qty.max' => 'Maksimal pembelian 5 tiket per transaksi.',
        ]);

        try {
            $booking = DB::transaction(function () use ($request, $event) {
                // Lock event row untuk menghindari race condition kuota
                $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

                if ($lockedEvent->quota < $request->ticket_qty) {
                    throw new \Exception('Maaf, kuota tiket yang tersedia tidak mencukupi.');
                }

                $orderId = 'ORDER-' . date('YmdHis') . '-' . rand(100, 999);
                $totalPrice = $lockedEvent->price * $request->ticket_qty;

                // Buat data booking dengan status PENDING
                return Booking::create([
                    'order_id' => $orderId,
                    'event_id' => $lockedEvent->id,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'ticket_qty' => $request->ticket_qty,
                    'total_price' => $totalPrice,
                    'status' => 'PENDING',
                ]);
            });

            // Minta Snap Token dari Midtrans
            $this->initMidtrans();
            $params = [
                'transaction_details' => [
                    'order_id' => $booking->order_id,
                    'gross_amount' => $booking->total_price,
                ],
                'customer_details' => [
                    'first_name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                ],
                'item_details' => [
                    [
                        'id' => $booking->event_id,
                        'price' => $event->price,
                        'quantity' => $booking->ticket_qty,
                        'name' => substr($event->title, 0, 50),
                    ]
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $booking->update(['snap_token' => $snapToken]);

            return redirect()->route('booking.payment', $booking->order_id);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman pembayaran yang memicu Pop-up Midtrans Snap.
     */
    public function showPayment($order_id)
    {
        $booking = Booking::with('event')->where('order_id', $order_id)->firstOrFail();

        if ($booking->status === 'SUCCESS') {
            return redirect()->route('booking.success', $booking->order_id);
        }

        if ($booking->status === 'FAILED' || $booking->status === 'EXPIRED') {
            return redirect()->route('booking.failed', $booking->order_id);
        }

        return view('events.payment', compact('booking'));
    }

    /**
     * Endpoint Webhook (HTTP Notification) untuk Midtrans Sandbox.
     */
    public function webhook(Request $request)
    {
        $this->initMidtrans();
        
        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid signature or payload'], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        $booking = Booking::where('order_id', $orderId)->first();
        if (!$booking) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            DB::transaction(function () use ($transaction, $fraud, $type, $booking, $notif) {
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();
                $lockedEvent = Event::where('id', $lockedBooking->event_id)->lockForUpdate()->first();

                if ($transaction == 'capture') {
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            $lockedBooking->update(['status' => 'PENDING']);
                        } else {
                            $this->markAsSuccess($lockedBooking, $lockedEvent, $notif->transaction_id);
                        }
                    }
                } else if ($transaction == 'settlement') {
                    $this->markAsSuccess($lockedBooking, $lockedEvent, $notif->transaction_id);
                } else if ($transaction == 'pending') {
                    $lockedBooking->update(['status' => 'PENDING']);
                } else if ($transaction == 'deny') {
                    $lockedBooking->update(['status' => 'FAILED']);
                } else if ($transaction == 'expire') {
                    $lockedBooking->update(['status' => 'EXPIRED']);
                } else if ($transaction == 'cancel') {
                    $lockedBooking->update(['status' => 'FAILED']);
                }
            });

            return response()->json(['message' => 'Webhook handled successfully']);

        } catch (\Exception $e) {
            Log::error('Midtrans Transaction Handling Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing transaction'], 500);
        }
    }

    /**
     * Helper untuk mengubah status booking menjadi sukses dan memotong kuota.
     */
    private function markAsSuccess($booking, $event, $transactionId)
    {
        if ($booking->status !== 'SUCCESS') {
            if ($event->quota < $booking->ticket_qty) {
                throw new \Exception('Kuota tidak mencukupi untuk pemesanan ini.');
            }
            $event->decrement('quota', $booking->ticket_qty);
            $booking->update([
                'status' => 'SUCCESS',
                'midtrans_trans_id' => $transactionId
            ]);
        }
    }

    /**
     * Tampilkan halaman sukses.
     * Sebagai fallback lokal (tanpa Ngrok), kita langsung cek status transaksi ke API Midtrans.
     */
    public function showSuccess($order_id)
    {
        $booking = Booking::with('event')->where('order_id', $order_id)->firstOrFail();

        // Fallback check jika status masih PENDING (membantu testing lokal tanpa Ngrok)
        if ($booking->status === 'PENDING') {
            $this->initMidtrans();
            try {
                $status = \Midtrans\Transaction::status($booking->order_id);
                
                $transaction = $status->transaction_status;
                $fraud = $status->fraud_status;
                $type = $status->payment_type;
                
                if ($transaction == 'settlement' || ($transaction == 'capture' && $type == 'credit_card' && $fraud == 'accept')) {
                    DB::transaction(function () use ($booking, $status) {
                        $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();
                        $lockedEvent = Event::where('id', $lockedBooking->event_id)->lockForUpdate()->first();
                        $this->markAsSuccess($lockedBooking, $lockedEvent, $status->transaction_id);
                    });
                    // Refresh data
                    $booking = Booking::with('event')->where('order_id', $order_id)->first();
                }
            } catch (\Exception $e) {
                Log::warning('Midtrans status check fallback failed: ' . $e->getMessage());
            }
        }

        if ($booking->status !== 'SUCCESS') {
            return redirect()->route('booking.payment', $booking->order_id);
        }

        return view('events.success', compact('booking'));
    }

    /**
     * Tampilkan halaman pending.
     */
    public function showPending($order_id)
    {
        $booking = Booking::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('events.pending', compact('booking'));
    }

    /**
     * Tampilkan halaman gagal.
     */
    public function showFailed($order_id)
    {
        $booking = Booking::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('events.failed', compact('booking'));
    }
}
