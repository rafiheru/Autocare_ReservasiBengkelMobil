@extends('layouts.admin')
@section('title','Kelola Booking')
@section('portal')
<x-page-heading eyebrow="OPERASIONAL" title="Booking pelanggan" description="Tinjau jadwal dan perbarui status reservasi."/>
<div class="content-card">
    @if($bookings->isEmpty())
        <div class="admin-booking-empty"><x-empty-state title="Belum ada booking masuk" description="Reservasi customer akan muncul di sini setelah mereka menentukan kendaraan, layanan, dan jadwal."/></div>
    @else
        <div class="table-scroll"><table><thead><tr><th>ID</th><th>Pelanggan</th><th>Kendaraan</th><th>Layanan</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
        @foreach($bookings as $booking)
            <tr><td><b>#{{ str_pad($booking->id,5,'0',STR_PAD_LEFT) }}</b></td><td>{{ $booking->customer->user->name }}<small class="table-sub">{{ $booking->customer->user->email }}</small></td><td>{{ $booking->vehicle->license_plate }}<small class="table-sub">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</small></td><td>{{ $booking->service->name }}</td><td>{{ $booking->booking_date->format('d M Y') }}<small class="table-sub">{{ substr($booking->booking_time,0,5) }} WIB</small></td><td><x-status :status="$booking->status"/></td><td><a class="row-link" href="{{ route('admin.bookings.show',$booking) }}">Kelola →</a></td></tr>
        @endforeach
        </tbody></table></div>
        <div class="pagination-wrap">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
