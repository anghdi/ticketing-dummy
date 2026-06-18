@extends('layouts.app')

@section('title', 'Metode Pembayaran')

@section('content')
    <div class="simulator-panel animate-fade-up" style="max-width: 550px;">
        <div class="simulator-header">
            <span class="simulator-badge">Midtrans Sandbox Mode</span>
            <h1 style="color: #0f172a; margin-bottom: 8px;">Selesaikan Pembayaran Anda</h1>
            <p style="color: #64748b; font-size: 0.95rem;">
                Silakan lakukan pembayaran menggunakan kartu kredit sandbox atau metode simulasi lainnya.
            </p>
        </div>

        <div class="result-ticket-details" style="text-align: left; margin-bottom: 30px;">
            <div class="result-detail-row">
                <span class="result-detail-label">Order ID</span>
                <span class="result-detail-val" style="color: var(--accent); font-weight: 700;">{{ $booking->order_id }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Nama Acara</span>
                <span class="result-detail-val" style="font-weight: 600;">{{ $booking->event->title }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Nama Pelanggan</span>
                <span class="result-detail-val">{{ $booking->customer_name }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">WhatsApp / Email</span>
                <span class="result-detail-val">{{ $booking->customer_phone }} / {{ $booking->customer_email }}</span>
            </div>
            <div class="result-detail-row">
                <span class="result-detail-label">Jumlah Tiket</span>
                <span class="result-detail-val">{{ $booking->ticket_qty }} Tiket</span>
            </div>
            <div class="result-detail-row" style="border-bottom: none; font-size: 1.15rem; padding-top: 15px; margin-top: 5px;">
                <span class="result-detail-label" style="font-weight: 700; color: #0f172a;">Total Tagihan</span>
                <span class="result-detail-val" style="color: var(--accent); font-weight: 800;">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 25px; line-height: 1.5;">
            Pop-up pembayaran Midtrans akan terbuka secara otomatis. Jika tidak terbuka, klik tombol di bawah untuk membuka kembali.
        </p>

        <div class="simulator-actions" style="margin-top: 20px;">
            <button id="pay-button" class="btn btn-primary" style="padding: 14px 28px; width: 100%;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Bayar Sekarang
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const snapToken = '{{ $booking->snap_token }}';
            const orderId = '{{ $booking->order_id }}';

            function triggerSnap() {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = `/booking/${orderId}/success`;
                    },
                    onPending: function(result) {
                        window.location.href = `/booking/${orderId}/pending`;
                    },
                    onError: function(result) {
                        window.location.href = `/booking/${orderId}/failed`;
                    },
                    onClose: function() {
                        console.log('User closed the snap popup without finishing payment.');
                    }
                });
            }

            // Auto trigger on load
            triggerSnap();

            // Re-trigger on click
            payButton.addEventListener('click', function() {
                triggerSnap();
            });
        });
    </script>
@endsection
