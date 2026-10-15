@extends('layouts.admin')
@section('title','Admin Dashboard')
@section('portal')
<x-page-heading eyebrow="WORKSHOP CONTROL" title="Dashboard operasional" description="Ringkasan aktivitas bengkel AutoCare hari ini."/>
<div class="stat-grid stat-grid-admin">
    @foreach([['Total booking',$stats['total_bookings'],'▤','blue'],['Booking hari ini',$stats['today_bookings'],'◷','orange'],['Pelanggan',$stats['customers'],'♙','green'],['Kendaraan',$stats['vehicles'],'▣','purple'],['Riwayat servis',$stats['records'],'⚙','orange'],['Testimoni',$stats['testimonials'],'☆','green'],['Rata-rata rating',$stats['rating'].' / 5','★','blue']] as [$label,$value,$icon,$color])
    <article class="stat-card"><span class="stat-icon {{ $color }}">{{ $icon }}</span><small>{{ $label }}</small><strong>{{ $value }}</strong></article>
    @endforeach
</div>
<div class="content-card"><div class="card-heading"><div><h2>Booking terbaru</h2><p>Reservasi yang masuk ke bengkel.</p></div><a class="text-link" href="{{ route('admin.bookings.index') }}">Kelola booking →</a></div>
@if($recent->isEmpty())<x-empty-state title="Belum ada booking" description="Booking pelanggan akan muncul di sini."/>@else
<div class="table-scroll"><table><thead><tr><th>Booking</th><th>Pelanggan</th><th>Kendaraan</th><th>Layanan</th><th>Jadwal</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($recent as $booking)<tr><td>#{{ str_pad($booking->id,5,'0',STR_PAD_LEFT) }}</td><td>{{ $booking->customer->user->name }}</td><td>{{ $booking->vehicle->license_plate }}</td><td>{{ $booking->service->name }}</td><td>{{ $booking->booking_date->format('d M Y') }} · {{ substr($booking->booking_time,0,5) }}</td><td><x-status :status="$booking->status"/></td><td><a class="row-link" href="{{ route('admin.bookings.show',$booking) }}">Detail →</a></td></tr>@endforeach
</tbody></table></div>@endif</div>
@endsection
