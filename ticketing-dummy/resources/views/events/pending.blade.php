@extends('layouts.app')

@section('title', 'Pembayaran Tertunda')

@section('content')
    <div class="result-card animate-fade-up">
        <div class="result-icon-wrapper" style="background-color: var(--info-bg); color: var(--info); border: 1px solid rgba(37, 99, 235, 0.15);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 32px; height: 32px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="result-title">Menunggu Pembayaran</h1>
        <p class="result-subtitle">Sistem sedang menunggu penyelesaian transaksi Anda di gerbang Midtrans.</p>

        <div class="result-ticket-details" style="margin-bottom: 30px;">
            <div class="result-detail-row">
                <span class="result-detail-label">Order ID</span>
                <span class="result-detail-val" style="color: var(--accent); font-weight: 700;">{{ $booking->order_id }}</span>
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
                <span class="result-detail-val" style="color: var(--accent); font-weight: 700;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="result-actions">
            <a href="{{ route('booking.payment', $booking->order_id) }}" class="btn btn-primary">Lanjutkan Pembayaran</a>
            <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>
    </div>
@endsection
