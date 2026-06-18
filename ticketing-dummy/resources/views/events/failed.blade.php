@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="result-card result-card-failed animate-fade-up">
        <div class="result-icon-wrapper result-icon-failed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <h1 class="result-title">Pembayaran Gagal / Batal</h1>
        <p class="result-subtitle">Transaksi Anda dibatalkan atau gagal diproses oleh gerbang pembayaran Midtrans.</p>

        <div class="result-ticket-details" style="margin-bottom: 30px;">
            <div class="result-detail-row">
                <span class="result-detail-label">Order ID</span>
                <span class="result-detail-val" style="color: var(--danger); font-weight: 700;">{{ $booking->order_id }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Event</span>
                <span class="result-detail-val" style="font-weight: 600;">{{ $booking->event->title }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Jumlah Tiket</span>
                <span class="result-detail-val">{{ $booking->ticket_qty }} Tiket</span>
            </div>
            <div class="result-detail-row" style="border-bottom: none;">
                <span class="result-detail-label">Total Tagihan</span>
                <span class="result-detail-val" style="color: var(--danger); font-weight: 700;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="result-actions">
            <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
            <a href="{{ route('checkout.show', $booking->event_id) }}" class="btn btn-danger">Coba Pesan Lagi</a>
        </div>
    </div>
@endsection
