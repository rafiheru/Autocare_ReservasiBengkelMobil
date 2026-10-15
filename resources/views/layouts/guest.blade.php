<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','AutoCare') · Bengkel Mobil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="site-shell">
    <svg class="icon-definitions" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="icon-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
        <symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></symbol>
        <symbol id="icon-car" viewBox="0 0 24 24"><path d="m5 11 1.5-5h11L19 11l2 2v5h-2v2h-3v-2H8v2H5v-2H3v-5l2-2Z"/><path d="M6 11h12M6 15h.01M18 15h.01"/></symbol>
        <symbol id="icon-booking" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="17" rx="2"/><path d="M8 2v4m8-4v4M4 9h16m-12 4h2m3 0h2m-7 4h2"/></symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="icon-star" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></symbol>
        <symbol id="icon-logout" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5m5 5H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></symbol>
        <symbol id="icon-service" viewBox="0 0 24 24"><path d="M14.5 6.5a5 5 0 0 0-6.7 6.7L3 18l3 3 4.8-4.8a5 5 0 0 0 6.7-6.7L14 13l-3-3 3.5-3.5Z"/></symbol>
        <symbol id="icon-customer" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0m3-13v6m-3-3h6"/></symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6"/></symbol>
        <symbol id="icon-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
    </svg>
    @if(!request()->routeIs('customer.*','admin.*'))
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>Auto<span class="brand-accent">Care</span><small>CARE THAT KEEPS YOU MOVING</small></span></a>
        <button class="nav-toggle" data-nav-toggle aria-label="Buka menu">☰</button>
        <nav class="main-nav" data-nav>
            @foreach([['Beranda',route('home'),'home'],['Servis Kami',route('home').'#services','services.*'],['Testimoni',route('home').'#testimonials','testimonials.public'],['Kontak',route('home').'#contact','contact'] ] as [$label,$url,$routePattern])
                <a class="{{ request()->routeIs($routePattern)?'active':'' }}" href="{{ $url }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="top-actions">
            @auth
                <a href="{{ auth()->user()->role==='admin'?route('admin.dashboard'):route('customer.dashboard') }}" class="text-link">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-link">Masuk</a>
            @endauth
            <a href="{{ auth()->check()?(auth()->user()->role==='admin'?route('admin.dashboard'):route('customer.bookings.create')):route('login') }}" class="button button-primary button-small">Booking Sekarang <span>↗</span></a>
        </div>
    </header>
    @endif
    <main>
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="footer">
        <div class="footer-inner">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>Auto<span class="brand-accent">Care</span><small>CARE THAT KEEPS YOU MOVING</small></span></a>
            <p>Perawatan kendaraan yang jujur, profesional, dan dapat diandalkan.</p>
            <span>© {{ date('Y') }} AutoCare. Semua hak dilindungi.</span>
        </div>
    </footer>
</body>
</html>
