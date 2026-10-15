@extends('layouts.guest')
@section('content')
<div class="portal-wrap">
    <aside class="portal-sidebar admin-sidebar">
        <a class="brand sidebar-brand" href="{{ route('home') }}"><span class="brand-mark">A</span><span>Auto<span class="brand-accent">Care</span></span></a>
        <div class="sidebar-caption">WORKSHOP CONTROL</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard')?'selected':'' }}"><x-icon name="dashboard"/><span>Dashboard</span></a>
        <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*')?'selected':'' }}"><x-icon name="booking"/><span>Booking</span></a>
        <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*')?'selected':'' }}"><x-icon name="service"/><span>Servis</span></a>
        <a href="{{ route('admin.records.index') }}" class="{{ request()->routeIs('admin.records.*')?'selected':'' }}"><x-icon name="clock"/><span>Riwayat Servis</span></a>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*')?'selected':'' }}"><x-icon name="customer"/><span>Pelanggan</span></a>
        <a href="{{ route('admin.vehicles.index') }}" class="{{ request()->routeIs('admin.vehicles.*')?'selected':'' }}"><x-icon name="car"/><span>Kendaraan</span></a>
        <a href="{{ route('admin.testimonials.index') }}" class="{{ request()->routeIs('admin.testimonials.*')?'selected':'' }}"><x-icon name="star"/><span>Testimoni</span></a>
        <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile*')?'selected':'' }}"><x-icon name="user"/><span>Profil Admin</span></a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout"><x-icon name="logout"/><span>Keluar</span></button></form>
    </aside>
    <section class="portal-content">@yield('portal')</section>
</div>
@endsection
