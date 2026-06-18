@extends('layouts.app')

@section('title', 'Simulator Pembayaran DOKU')

@section('content')
    <div class="simulator-panel animate-fade-up">
        <div class="simulator-header">
            <span class="simulator-badge">Sandbox Simulator Mode</span>
            <h1 style="color: #fff; margin-bottom: 8px;">Gerbang Pembayaran MiniTick (DOKU Mock)</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Halaman ini menyimulasikan halaman eksternal DOKU Checkout untuk menyelesaikan pembayaran Anda secara asinkron.
            </p>
        </div>

        <div class="result-ticket-details" style="text-align: left; margin-bottom: 30px;">
            <div class="result-detail-row">
                <span class="result-detail-label">Nomor Invoice</span>
                <span class="result-detail-val" style="color: var(--accent);">{{ $booking->invoice_number }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Nama Acara</span>
                <span class="result-detail-val">{{ $booking->event->title }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Nama Pelanggan</span>
                <span class="result-detail-val">{{ $booking->customer_name }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Email / WhatsApp</span>
                <span class="result-detail-val">{{ $booking->customer_email }} / {{ $booking->customer_phone }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Jumlah Tiket</span>
                <span class="result-detail-val">{{ $booking->ticket_qty }} Tiket</span>
            </div>
            <div class="result-detail-row" style="border-bottom: none; font-size: 1.1rem; padding-top: 15px; margin-top: 5px;">
                <span class="result-detail-label" style="font-weight: 700; color: #fff;">Total Pembayaran</span>
                <span class="result-detail-val" style="color: var(--success); font-weight: 800; font-family: var(--font-display);">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5;">
            Silakan pilih tindakan simulasi di bawah untuk menguji alur respons webhook backend:
        </p>

        <div class="simulator-actions">
            <!-- Form untuk simulasikan bayar sukses -->
            <form action="{{ route('booking.simulate_success', $booking->invoice_number) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" style="padding: 14px 28px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simulasikan Sukses
                </button>
            </form>

            <!-- Form untuk simulasikan bayar gagal -->
            <form action="{{ route('booking.simulate_fail', $booking->invoice_number) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" style="padding: 14px 28px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simulasikan Gagal
                </button>
            </form>
        </div>
    </div>
@endsection
