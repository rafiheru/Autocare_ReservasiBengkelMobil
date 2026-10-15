@extends('layouts.guest')
@section('title','AutoCare — Bengkel mobil')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <div class="eyebrow eyebrow-light"><span class="pulse-dot"></span> PERAWATAN KENDARAAN</div>
        <h1>Perjalanan nyaman<br>dimulai dari <em>mobil sehat.</em></h1>
        <p>Atur jadwal servis, simpan kendaraan, dan ikuti progres pengerjaan dalam satu akun AutoCare.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ auth()->check()?(auth()->user()->role==='customer'?route('customer.bookings.create'):route('admin.dashboard')):route('login') }}">Booking servis <x-icon name="arrow"/></a>
            <a class="button button-glass" href="{{ route('home').'#services' }}">Lihat layanan <x-icon name="arrow"/></a>
        </div>
        <div class="hero-proof"><span class="hero-proof-icon"><x-icon name="check"/></span><span><strong>Semua tercatat di satu tempat</strong><small>Kendaraan · Booking · Riwayat servis</small></span></div>
    </div>
    <div class="hero-art" aria-label="Ilustrasi kendaraan AutoCare">
        <div class="art-ring ring-one"></div><div class="art-ring ring-two"></div><div class="art-glow"></div>
        <svg class="hero-car" viewBox="0 0 620 320" role="img" aria-label="Siluet mobil">
            <ellipse cx="312" cy="266" rx="236" ry="20" fill="#071b20" opacity=".55"/>
            <path d="M78 207 98 166q8-17 31-23l69-18 53-61q12-14 34-14h112q22 0 37 17l53 62 68 16q28 7 37 31l12 34v38H79z" fill="#e5f4f0" stroke="#70d5c6" stroke-width="5" stroke-linejoin="round"/>
            <path d="m216 130 47-53q8-9 23-9h91q14 0 23 10l43 52z" fill="#18343a" stroke="#76d5c9" stroke-width="4" stroke-linejoin="round"/>
            <path d="m276 70-2 60m99-60 32 60" fill="none" stroke="#76d5c9" stroke-width="4"/>
            <path d="M103 177h53m309 0h40" stroke="#f3b35e" stroke-width="11" stroke-linecap="round"/>
            <path d="M80 214h458" stroke="#a8d6ce" stroke-width="3"/>
            <circle cx="185" cy="230" r="43" fill="#132a30" stroke="#d9eeea" stroke-width="8"/><circle cx="185" cy="230" r="17" fill="#6dcabd"/>
            <circle cx="463" cy="230" r="43" fill="#132a30" stroke="#d9eeea" stroke-width="8"/><circle cx="463" cy="230" r="17" fill="#6dcabd"/>
            <path d="M112 196h58m324 0h37" stroke="#18343a" stroke-width="4" stroke-linecap="round"/>
        </svg>
        <div class="floating-card floating-card-top"><span class="float-icon"><x-icon name="booking"/></span><span><b>Status booking</b><small>Pantau dari dashboard</small></span></div>
        <div class="floating-card floating-card-bottom"><span class="float-icon orange"><x-icon name="clock"/></span><span><b>Riwayat servis</b><small>Tersimpan per kendaraan</small></span></div>
        <div class="art-stamp">AUTOCARE<br><small>VEHICLE SERVICE</small></div>
    </div>
</section>

<section class="trust-strip" aria-label="Fitur AutoCare">
    <div><x-icon name="car"/><span>Kendaraan tersimpan</span></div><i></i>
    <div><x-icon name="booking"/><span>Booking terjadwal</span></div><i></i>
    <div><x-icon name="clock"/><span>Progres servis tercatat</span></div><i></i>
    <div><x-icon name="service"/><span>Riwayat per kendaraan</span></div>
</section>

<section id="services" class="section section-services">
    <div class="section-heading"><div><div class="eyebrow">LAYANAN YANG TERSEDIA</div><h2>Servis tepat,<br><em>perjalanan mantap.</em></h2></div><p class="section-aside-note">Durasi dan estimasi biaya ditampilkan sebelum Anda membuat booking.</p></div>
    <div class="service-grid">
        @forelse($services as $service)
            <article class="service-card"><div class="service-card-top"><span class="service-symbol"><x-icon name="service"/></span><span class="service-availability">Tersedia untuk booking</span></div><h3>{{ $service->name }}</h3><p>{{ $service->description ?: 'Perawatan kendaraan sesuai kebutuhan servis Anda.' }}</p><div class="service-meta"><span><x-icon name="clock"/> {{ $service->duration }} menit</span><b>Rp {{ number_format($service->price,0,',','.') }}</b></div><a href="{{ route('services.show',$service) }}" class="card-link">Detail layanan <x-icon name="arrow"/></a></article>
        @empty
            <div class="service-empty"><x-empty-state title="Layanan belum tersedia" description="Daftar layanan akan muncul di sini setelah diperbarui oleh AutoCare."/></div>
        @endforelse
    </div>
</section>

<section class="why-section">
    <div class="why-copy"><div class="eyebrow">ALUR SERVIS AUTOCARE</div><h2>Dari booking sampai selesai,<br><em>semuanya tercatat.</em></h2><p>Informasi kendaraan disimpan di akun Anda. Setelah booking dikonfirmasi, bengkel dapat mencatat inspeksi dan progres pengerjaan untuk dilihat kembali.</p></div>
    <div class="service-journey">
        <article class="journey-step"><span>01</span><div><h3>Simpan kendaraan</h3><p>Daftarkan kendaraan sekali, lalu gunakan kembali saat booking.</p></div><x-icon name="car"/></article>
        <article class="journey-step"><span>02</span><div><h3>Buat booking</h3><p>Pilih layanan dan waktu yang tersedia untuk kendaraan Anda.</p></div><x-icon name="booking"/></article>
        <article class="journey-step"><span>03</span><div><h3>Pantau riwayat</h3><p>Lihat progres pengerjaan dan catatan servis setelah dibuat bengkel.</p></div><x-icon name="clock"/></article>
    </div>
</section>

<section class="cta-band"><div><div class="eyebrow eyebrow-light">JADWALKAN PERAWATAN</div><h2>Siapkan kendaraan<br>untuk perjalanan berikutnya.</h2></div><a class="button button-light" href="{{ auth()->check()&&auth()->user()->role==='customer'?route('customer.bookings.create'):route('login') }}">Mulai booking <x-icon name="arrow"/></a><div class="cta-decoration" aria-hidden="true">A</div></section>

<section id="testimonials" class="section testimonial-section">
    <div class="section-heading"><div><div class="eyebrow">CERITA PELANGGAN</div><h2>Pengalaman servis<br><em>dari pelanggan AutoCare.</em></h2></div><p class="section-aside-note">Testimoni yang ditampilkan telah dipublikasikan oleh admin.</p></div>
    <div class="testimonial-grid">
        @forelse($testimonials as $testimonial)
            <article class="testimonial-card"><div class="stars" aria-label="Rating {{ $testimonial->rating }} dari 5">{{ str_repeat('★',$testimonial->rating) }}<span>{{ str_repeat('★',5-$testimonial->rating) }}</span></div><p>“{{ $testimonial->comment }}”</p><div class="testimonial-author"><span class="author-avatar">{{ strtoupper(substr($testimonial->customer->user->name,0,1)) }}</span><div><b>{{ $testimonial->customer->user->name }}</b><small>{{ $testimonial->booking->vehicle->brand }} {{ $testimonial->booking->vehicle->model }}</small></div></div></article>
        @empty
            <div class="testimonial-empty"><x-empty-state title="Belum ada testimoni" description="Testimoni pelanggan akan tampil di sini setelah dipublikasikan."/></div>
        @endforelse
    </div>
</section>

<section id="contact" class="contact-band"><div><div class="eyebrow">KONTAK AUTOCARE</div><h2>Informasi bengkel</h2><p>Alamat dan nomor kontak akan ditampilkan setelah data resmi bengkel ditambahkan.</p></div><span class="contact-note">Informasi kontak belum tersedia</span></section>
@endsection
