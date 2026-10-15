@extends('layouts.app')
@section('title','Booking Servis')
@section('portal')
<x-page-heading eyebrow="RESERVASI SERVIS" title="Booking servis" description="Pilih kendaraan, layanan, dan waktu yang paling sesuai."/>
<div class="booking-steps"><div class="step active"><span>01</span><small>Kendaraan</small></div><i></i><div class="step active"><span>02</span><small>Layanan</small></div><i></i><div class="step active"><span>03</span><small>Jadwal</small></div><i></i><div class="step"><span>04</span><small>Konfirmasi</small></div></div>
@if($errors->any())<div class="form-alert">{{ $errors->first() }}</div>@endif
@if($vehicles->isEmpty())
    <div class="content-card"><x-empty-state title="Tambahkan kendaraan terlebih dahulu" description="Booking menggunakan kendaraan yang tersimpan di akun Anda."><a class="button button-primary" href="{{ route('customer.vehicles.create') }}">＋ Tambah kendaraan</a></x-empty-state></div>
@elseif($services->isEmpty())
    <div class="content-card"><x-empty-state title="Layanan belum tersedia" description="Belum ada layanan aktif untuk dipilih."/></div>
@else
<div class="booking-layout">
    <form class="form-card booking-form" method="POST" action="{{ route('customer.bookings.store') }}" data-booking-form>@csrf
        <div class="form-section-title"><span>01</span><div><h2>Pilih kendaraan</h2><p>Pilih kendaraan yang akan diservis.</p></div></div>
        <div class="vehicle-choice-grid">
            @foreach($vehicles as $i=>$vehicle)
            <label class="vehicle-choice"><input type="radio" name="vehicle_id" value="{{ $vehicle->id }}" {{ old('vehicle_id')==$vehicle->id||(!$i&&!old('vehicle_id'))?'checked':'' }} required><span class="choice-content"><span class="choice-car"><x-icon name="car"/></span><b>{{ $vehicle->brand }} {{ $vehicle->model }}</b><small>{{ $vehicle->license_plate }} · {{ $vehicle->year }}</small></span></label>
            @endforeach
        </div>
        <div class="form-section-title spaced"><span>02</span><div><h2>Pilih layanan</h2><p>Estimasi durasi dan biaya tampil di setiap pilihan.</p></div></div>
        <div class="service-choice-list">
            @foreach($services as $i=>$service)
            <label class="service-choice"><input type="radio" name="service_id" value="{{ $service->id }}" data-service-name="{{ $service->name }}" data-service-price="Rp {{ number_format($service->price,0,',','.') }}" {{ old('service_id')==$service->id||(!$selectedService&& !$i&&!old('service_id'))||$selectedService==$service->id?'checked':'' }} required><span class="choice-radio"></span><span class="service-choice-copy"><b>{{ $service->name }}</b><small>{{ $service->duration }} menit · Rp {{ number_format($service->price,0,',','.') }}</small></span><span class="choice-arrow">↗</span></label>
            @endforeach
        </div>
        <div class="form-section-title spaced"><span>03</span><div><h2>Pilih jadwal</h2><p>Waktu akan dicek ulang saat booking dikirim.</p></div></div>
        <div class="form-grid"><div class="field"><label>Tanggal servis</label><input type="date" name="booking_date" min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}" required></div><div class="field"><label>Waktu servis</label><input type="time" name="booking_time" value="{{ old('booking_time') }}" required></div><div class="field field-wide"><label>Keluhan / catatan <span class="optional">Opsional</span></label><textarea name="complaint" rows="3" placeholder="Ceritakan gejala atau kebutuhan kendaraan Anda...">{{ old('complaint') }}</textarea></div></div>
        <div class="booking-note"><span>ⓘ</span><p>Booking Anda akan berstatus <b>Menunggu</b> sampai dikonfirmasi oleh admin bengkel.</p></div>
        <div class="form-actions"><a href="{{ route('customer.bookings.index') }}" class="button button-outline">Batal</a><button class="button button-primary">Konfirmasi booking <span>↗</span></button></div>
    </form>
    <aside class="booking-aside"><div class="aside-icon">✦</div><h3>Ringkasan pilihan</h3><p>Kendaraan: <b data-summary-vehicle>—</b></p><p>Layanan: <b data-summary-service>—</b></p><p>Tanggal: <b data-summary-date>—</b></p><p>Waktu: <b data-summary-time>—</b></p><div class="aside-divider"></div><small>PERLU BANTUAN?</small><a href="{{ route('contact') }}">Hubungi AutoCare →</a></aside>
</div>
@endif
@endsection
