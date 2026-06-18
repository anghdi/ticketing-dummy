@extends('layouts.app')

@section('title', 'Checkout - ' . $event->title)

@section('content')
    <a href="{{ route('home') }}" class="btn btn-secondary" style="margin-bottom: 24px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Katalog
    </a>

    <div class="checkout-wrapper animate-fade-up">
        <!-- Main Form Column -->
        <div class="checkout-card">
            <h2 class="checkout-title">Form Data Diri</h2>
            
            <form action="{{ route('checkout.store', $event->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="customer_name" class="form-label">Nama Lengkap</label>
                    <input type="text" 
                           id="customer_name" 
                           name="customer_name" 
                           class="form-control @error('customer_name') is-invalid @enderror" 
                           placeholder="Masukkan nama lengkap Anda" 
                           value="{{ old('customer_name') }}" 
                           required>
                    @error('customer_name')
                        <div class="form-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="customer_email" class="form-label">Alamat Email</label>
                    <input type="email" 
                           id="customer_email" 
                           name="customer_email" 
                           class="form-control @error('customer_email') is-invalid @enderror" 
                           placeholder="contoh@email.com" 
                           value="{{ old('customer_email') }}" 
                           required>
                    @error('customer_email')
                        <div class="form-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="customer_phone" class="form-label">Nomor WhatsApp (WhatsApp Aktif)</label>
                    <input type="tel" 
                           id="customer_phone" 
                           name="customer_phone" 
                           class="form-control @error('customer_phone') is-invalid @enderror" 
                           placeholder="Contoh: 081234567890" 
                           value="{{ old('customer_phone') }}" 
                           required>
                    @error('customer_phone')
                        <div class="form-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="ticket_qty" class="form-label">Jumlah Tiket (Maksimal 5 tiket)</label>
                    <select id="ticket_qty" name="ticket_qty" class="form-control @error('ticket_qty') is-invalid @enderror">
                        @for ($i = 1; $i <= min(5, $event->quota); $i++)
                            <option value="{{ $i }}" {{ old('ticket_qty') == $i ? 'selected' : '' }}>
                                {{ $i }} Tiket
                            </option>
                        @endfor
                    </select>
                    @error('ticket_qty')
                        <div class="form-error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 10px;">
                    Bayar Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Sidebar Summary Column -->
        <div class="checkout-card">
            <h3 class="checkout-summary-title">Ringkasan Pemesanan</h3>
            
            <div class="checkout-event-details">
                @if ($event->image_url)
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="checkout-event-img">
                @else
                    <div class="checkout-event-img" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);"></div>
                @endif
                <div class="checkout-event-info">
                    <h4>{{ $event->title }}</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $event->location }}</p>
                </div>
            </div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span class="summary-row-label">Harga per Tiket</span>
                    <span class="summary-row-val">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Jumlah Tiket</span>
                    <span class="summary-row-val" id="summary-qty">1 Tiket</span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Subtotal</span>
                    <span class="summary-row-val" id="summary-subtotal">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-total">
                <span class="summary-total-label">Total Pembayaran</span>
                <span class="summary-total-val" id="summary-total-price">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtySelect = document.getElementById('ticket_qty');
            const summaryQty = document.getElementById('summary-qty');
            const summarySubtotal = document.getElementById('summary-subtotal');
            const summaryTotal = document.getElementById('summary-total-price');
            
            const ticketPrice = {{ $event->price }};

            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            qtySelect.addEventListener('change', function() {
                const qty = parseInt(qtySelect.value);
                const subtotal = ticketPrice * qty;

                summaryQty.textContent = qty + ' Tiket';
                summarySubtotal.textContent = formatRupiah(subtotal);
                summaryTotal.textContent = formatRupiah(subtotal);
            });
        });
    </script>
@endsection
