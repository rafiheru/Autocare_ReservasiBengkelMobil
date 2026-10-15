@extends('layouts.guest')
@section('content')
<div class="portal-wrap">
    <aside class="portal-sidebar">
        <a class="brand sidebar-brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>Auto<span class="brand-accent">Care</span></span></a>
        <div class="sidebar-caption">MENU PELANGGAN</div>
        <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard')?'selected':'' }}"><x-icon name="dashboard"/><span>Dashboard</span></a>
        <a href="{{ route('customer.profile') }}" class="{{ request()->routeIs('customer.profile*')?'selected':'' }}"><x-icon name="user"/><span>Profil</span></a>
        <a href="{{ route('customer.vehicles.index') }}" class="{{ request()->routeIs('customer.vehicles.*')?'selected':'' }}"><x-icon name="car"/><span>Kendaraan Saya</span></a>
        <a href="{{ route('customer.bookings.index') }}" class="{{ request()->routeIs('customer.bookings.*')?'selected':'' }}"><x-icon name="booking"/><span>Booking Saya</span></a>
        <a href="{{ route('customer.history.index') }}" class="{{ request()->routeIs('customer.history.*')?'selected':'' }}"><x-icon name="clock"/><span>Riwayat Servis</span></a>
        <a href="{{ route('customer.testimonials.index') }}" class="{{ request()->routeIs('customer.testimonials.*')?'selected':'' }}"><x-icon name="star"/><span>Testimoni</span></a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout"><x-icon name="logout"/><span>Keluar</span></button></form>
    </aside>
    <section class="portal-content">@yield('portal')</section>
</div>
@endsection
