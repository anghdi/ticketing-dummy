@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="result-card result-card-failed animate-fade-up">
        <div class="result-icon-wrapper result-icon-failed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <h1 class="result-title">Pembayaran Gagal</h1>
        <p class="result-subtitle">Sistem kami mendeteksi transaksi dibatalkan atau gagal diproses.</p>

        <div class="result-ticket-details" style="margin-bottom: 30px;">
            <div class="result-detail-row">
                <span class="result-detail-label">Nomor Invoice</span>
                <span class="result-detail-val" style="color: var(--danger);">{{ $booking->invoice_number }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Event</span>
                <span class="result-detail-val">{{ $booking->event->title }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Jumlah Tiket</span>
                <span class="result-detail-val">{{ $booking->ticket_qty }} Tiket</span>
            </div>
            <div class="result-detail-row" style="border-bottom: none;">
                <span class="result-detail-label">Total Pembayaran</span>
                <span class="result-detail-val" style="color: var(--danger);">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="result-actions">
            <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
            <a href="{{ route('checkout.show', $booking->event_id) }}" class="btn btn-danger">Coba Pesan Lagi</a>
        </div>
    </div>
@endsection
