<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketingController extends Controller
{
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
     * Proses pembuatan booking (status PENDING).
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
            'customer_phone.required' => 'Nomor WhatsApp wajib diisi.',
            'customer_phone.min' => 'Nomor WhatsApp minimal 9 digit.',
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

                $invoiceNumber = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
                $totalPrice = $lockedEvent->price * $request->ticket_qty;

                // Buat data booking dengan status PENDING
                return Booking::create([
                    'invoice_number' => $invoiceNumber,
                    'event_id' => $lockedEvent->id,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'ticket_qty' => $request->ticket_qty,
                    'total_price' => $totalPrice,
                    'status' => 'PENDING',
                ]);
            });

            return redirect()->route('booking.simulator', $booking->invoice_number);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman simulator pembayaran (Mock DOKU).
     */
    public function showSimulator($invoice_number)
    {
        $booking = Booking::with('event')->where('invoice_number', $invoice_number)->firstOrFail();

        if ($booking->status === 'SUCCESS') {
            return redirect()->route('booking.success', $booking->invoice_number);
        }

        if ($booking->status === 'FAILED') {
            return redirect()->route('booking.failed', $booking->invoice_number);
        }

        return view('events.simulator', compact('booking'));
    }

    /**
     * Simulasikan pembayaran sukses (Mock Webhook / Callback DOKU).
     */
    public function simulateSuccess($invoice_number)
    {
        $booking = Booking::where('invoice_number', $invoice_number)->firstOrFail();

        if ($booking->status !== 'PENDING') {
            return redirect()->route('booking.simulator', $booking->invoice_number);
        }

        try {
            DB::transaction(function () use ($booking) {
                // Lock booking & event row
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();
                $lockedEvent = Event::where('id', $lockedBooking->event_id)->lockForUpdate()->first();

                if ($lockedEvent->quota < $lockedBooking->ticket_qty) {
                    throw new \Exception('Kuota tiket tidak mencukupi untuk menyelesaikan transaksi.');
                }

                // Kurangi kuota event
                $lockedEvent->decrement('quota', $lockedBooking->ticket_qty);

                // Update booking status
                $lockedBooking->update([
                    'status' => 'SUCCESS',
                    'doku_trans_id' => 'DOKU-MOCK-' . strtoupper(Str::random(10)),
                ]);
            });

            return redirect()->route('booking.success', $booking->invoice_number)->with('success', 'Pembayaran sukses disimulasikan!');

        } catch (\Exception $e) {
            return redirect()->route('booking.simulator', $booking->invoice_number)->with('error', $e->getMessage());
        }
    }

    /**
     * Simulasikan pembayaran gagal.
     */
    public function simulateFail($invoice_number)
    {
        $booking = Booking::where('invoice_number', $invoice_number)->firstOrFail();

        if ($booking->status !== 'PENDING') {
            return redirect()->route('booking.simulator', $booking->invoice_number);
        }

        $booking->update([
            'status' => 'FAILED',
        ]);

        return redirect()->route('booking.failed', $booking->invoice_number)->with('info', 'Pembayaran gagal disimulasikan.');
    }

    /**
     * Tampilkan halaman landing sukses.
     */
    public function showSuccess($invoice_number)
    {
        $booking = Booking::with('event')->where('invoice_number', $invoice_number)->firstOrFail();

        if ($booking->status !== 'SUCCESS') {
            return redirect()->route('booking.simulator', $booking->invoice_number);
        }

        return view('events.success', compact('booking'));
    }

    /**
     * Tampilkan halaman landing gagal.
     */
    public function showFailed($invoice_number)
    {
        $booking = Booking::with('event')->where('invoice_number', $invoice_number)->firstOrFail();

        if ($booking->status !== 'FAILED') {
            return redirect()->route('booking.simulator', $booking->invoice_number);
        }

        return view('events.failed', compact('booking'));
    }
}
