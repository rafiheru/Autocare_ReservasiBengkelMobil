@extends('layouts.admin')
@section('title',$record->exists?'Perbarui Servis':'Catat Servis')
@section('portal')
<a href="{{ route('admin.records.index') }}" class="back-link">← Riwayat servis</a>
<x-page-heading eyebrow="PROSES PENGERJAAN" title="{{ $record->exists?'Perbarui catatan servis':'Buat catatan servis' }}" description="Catatan servis hanya dapat dibuat untuk booking yang sudah dikonfirmasi."/>
<div class="form-card narrow-card">
    <form method="POST" action="{{ $record->exists?route('admin.records.update',$record):route('admin.records.store') }}">
        @csrf
        @if($record->exists)
            @method('PUT')
        @endif
        @if(!$record->exists)
            <div class="field"><label>Booking terkonfirmasi</label><select name="booking_id" required><option value="">Pilih booking</option>@foreach($bookings as $booking)<option value="{{ $booking->id }}">#{{ str_pad($booking->id,5,'0',STR_PAD_LEFT) }} · {{ $booking->customer->user->name }} · {{ $booking->vehicle->license_plate }} · {{ $booking->service->name }}</option>@endforeach</select>@error('booking_id')<small class="field-error">{{ $message }}</small>@enderror</div>
        @else
            <div class="booking-note"><span>#{{ str_pad($record->booking_id,5,'0',STR_PAD_LEFT) }}</span><p>{{ $record->vehicle->license_plate }} · {{ $record->service->name }} · status booking {{ $record->booking->status }}</p></div>
        @endif
        <div class="field"><label>Hasil inspeksi</label><textarea name="inspection" rows="3">{{ old('inspection',$record->inspection) }}</textarea></div>
        <div class="field"><label>Pengerjaan</label><textarea name="work_done" rows="3">{{ old('work_done',$record->work_done) }}</textarea></div>
        <div class="field"><label>Catatan tambahan</label><textarea name="notes" rows="3">{{ old('notes',$record->notes) }}</textarea></div>
        <div class="field"><label>Tanggal servis</label><input type="date" name="service_date" value="{{ old('service_date',optional($record->service_date)->format('Y-m-d')) }}"></div>
        <div class="form-actions"><a href="{{ route('admin.records.index') }}" class="button button-outline">Batal</a><button class="button button-primary">Simpan catatan <span>↗</span></button></div>
    </form>
    @if($record->exists)
        <div class="status-management"><h3>Progres servis</h3><p>Status saat ini: <x-status :status="$record->status"/></p>
            @php($nextStatus=['waiting'=>'inspection','inspection'=>'processing','processing'=>'completed','completed'=>'picked_up'][$record->status]??null)
            @if($nextStatus)
                <form method="POST" action="{{ route('admin.records.status',$record) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $nextStatus }}"><button class="button button-outline">Tandai {{ ['inspection'=>'inspeksi','processing'=>'diproses','completed'=>'selesai','picked_up'=>'diambil'][$nextStatus] }} →</button></form>
            @endif
        </div>
    @endif
</div>
@endsection
