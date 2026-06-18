@extends('layouts.app')

@section('title', 'Katalog Event')

@section('content')
    <section class="hero-section animate-fade-up">
        <h1 class="hero-title">Temukan Event Seru Kamu</h1>
        <p class="hero-subtitle">Beli tiket konser, konferensi, dan workshop terpopuler secara instan tanpa ribet.</p>
    </section>

    <div class="event-grid animate-fade-up">
        @forelse ($events as $event)
            <div class="event-card">
                <div class="event-card-img-wrapper">
                    @if ($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="event-card-img">
                    @else
                        <div class="event-card-img" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); width: 100%; height: 100%;"></div>
                    @endif

                    @if ($event->quota <= 0)
                        <span class="event-card-badge badge-soldout">Habis</span>
                    @elseif ($event->quota <= 10)
                        <span class="event-card-badge badge-limited">Terbatas</span>
                    @else
                        <span class="event-card-badge badge-available">Tersedia</span>
                    @endif
                </div>

                <div class="event-card-content">
                    <h3 class="event-title">{{ $event->title }}</h3>
                    
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <!-- Pin icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $event->location }}</span>
                        </div>
                        <div class="event-meta-item">
                            <!-- Calendar icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $event->date_time->format('d M Y - H:i') }} WIB</span>
                        </div>
                        <div class="event-meta-item">
                            <!-- Ticket icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Kuota: <strong>{{ $event->quota }} tiket tersisa</strong></span>
                        </div>
                    </div>

                    <div class="event-card-footer">
                        <div class="event-price">
                            <span class="event-price-label">Mulai dari</span>
                            <span class="event-price-val">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                        </div>

                        @if ($event->quota > 0)
                            <a href="{{ route('checkout.show', $event->id) }}" class="btn btn-primary">Beli Tiket</a>
                        @else
                            <button class="btn btn-primary" disabled>Habis Terjual</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
                <p>Tidak ada event yang ditemukan.</p>
            </div>
        @endforelse
    </div>
@endsection
