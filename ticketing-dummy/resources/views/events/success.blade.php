@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="result-card result-card-success animate-fade-up">
        <div class="result-icon-wrapper result-icon-success">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="result-title">E-Tiket Diterbitkan!</h1>
        <p class="result-subtitle">Pembayaran Anda telah diverifikasi oleh sistem secara otomatis.</p>

        <div class="result-ticket-details">
            <div class="result-detail-row">
                <span class="result-detail-label">Nomor Invoice</span>
                <span class="result-detail-val" style="color: var(--accent);">{{ $booking->invoice_number }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">DOKU Trans ID</span>
                <span class="result-detail-val" style="font-family: monospace; font-size: 0.85rem;">{{ $booking->doku_trans_id }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Event</span>
                <span class="result-detail-val">{{ $booking->event->title }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Waktu Event</span>
                <span class="result-detail-val">{{ $booking->event->date_time->format('d M Y, H:i') }} WIB</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Nama Pembeli</span>
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
            <div class="result-detail-row">
                <span class="result-detail-label">Total Pembayaran</span>
                <span class="result-detail-val" style="color: var(--success);">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>

            <div class="ticket-divider"></div>

            <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px; font-weight: 600;">
                Tunjukkan QR Code ini di Loket Masuk:
            </p>

            <div class="result-qr-wrapper">
                <div class="result-qr-mock">
                    <!-- Clean SVG mock QR Code -->
                    <svg viewBox="0 0 100 100" style="width: 100%; height: 100%; display: block;">
                        <!-- Border and positioning marks -->
                        <rect x="0" y="0" width="25" height="25" fill="#0b0f19" />
                        <rect x="3" y="3" width="19" height="19" fill="#fff" />
                        <rect x="6" y="6" width="13" height="13" fill="#0b0f19" />
                        
                        <rect x="75" y="0" width="25" height="25" fill="#0b0f19" />
                        <rect x="78" y="3" width="19" height="19" fill="#fff" />
                        <rect x="81" y="6" width="13" height="13" fill="#0b0f19" />

                        <rect x="0" y="75" width="25" height="25" fill="#0b0f19" />
                        <rect x="3" y="78" width="19" height="19" fill="#fff" />
                        <rect x="6" y="81" width="13" height="13" fill="#0b0f19" />

                        <!-- Mock Data matrix dots -->
                        <rect x="35" y="5" width="6" height="6" fill="#0b0f19" />
                        <rect x="45" y="10" width="6" height="12" fill="#0b0f19" />
                        <rect x="60" y="5" width="10" height="6" fill="#0b0f19" />
                        <rect x="55" y="15" width="12" height="6" fill="#0b0f19" />
                        
                        <rect x="5" y="35" width="6" height="6" fill="#0b0f19" />
                        <rect x="15" y="45" width="12" height="6" fill="#0b0f19" />
                        <rect x="35" y="35" width="15" height="15" fill="#0b0f19" />
                        <rect x="60" y="30" width="6" height="12" fill="#0b0f19" />
                        <rect x="85" y="35" width="10" height="10" fill="#0b0f19" />

                        <rect x="30" y="60" width="12" height="6" fill="#0b0f19" />
                        <rect x="5" y="65" width="6" height="6" fill="#0b0f19" />
                        <rect x="50" y="55" width="18" height="18" fill="#0b0f19" />
                        <rect x="75" y="55" width="6" height="12" fill="#0b0f19" />
                        
                        <rect x="35" y="80" width="10" height="10" fill="#0b0f19" />
                        <rect x="80" y="80" width="15" height="6" fill="#0b0f19" />
                        <rect x="75" y="90" width="6" height="8" fill="#0b0f19" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="result-actions">
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
            <button onclick="window.print()" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Tiket
            </button>
        </div>
    </div>
@endsection
