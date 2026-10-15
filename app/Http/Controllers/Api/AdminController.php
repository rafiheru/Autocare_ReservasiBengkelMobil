<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Booking, Customer, Service, ServiceRecord, Testimonial, Vehicle};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard() { return response()->json(['success'=>true,'data'=>['total_bookings'=>Booking::count(),'today_bookings'=>Booking::whereDate('booking_date',today())->count(),'total_service_records'=>ServiceRecord::count(),'completed_service_records'=>ServiceRecord::whereIn('status',['completed','picked_up'])->count(),'total_customers'=>Customer::count(),'total_vehicles'=>Vehicle::count(),'total_testimonials'=>Testimonial::count(),'average_rating'=>round(Testimonial::where('status','published')->avg('rating')??0,2),'recent_bookings'=>Booking::with(['customer.user','vehicle','service'])->latest()->limit(10)->get()]]); }
    public function bookings() { return response()->json(['success'=>true,'data'=>Booking::with(['customer.user','vehicle','service','serviceRecord'])->latest()->paginate(20)]); }
    public function showBooking(Booking $booking) { return response()->json(['success'=>true,'data'=>$booking->load(['customer.user','vehicle','service','serviceRecord','testimonial'])]); }
    public function changeBookingStatus(Request $r, Booking $booking) { $d=$r->validate(['status'=>'required|in:confirmed,cancelled']); if($booking->status!=='pending')throw ValidationException::withMessages(['status'=>['Status booking ini tidak dapat diubah lagi.']]);$booking->update($d);return response()->json(['success'=>true,'data'=>$booking->fresh()]); }
    public function services() { return response()->json(['success'=>true,'data'=>Service::orderBy('name')->paginate(20)]); }
    public function showService(Service $service) { return response()->json(['success'=>true,'data'=>$service]); }
    public function storeService(Request $r) { $d=$r->validate(['name'=>'required|string|max:255','description'=>'nullable|string','duration'=>'required|integer|min:1','price'=>'required|numeric|min:0','status'=>'required|in:active,inactive']);return response()->json(['success'=>true,'data'=>Service::create($d)],201); }
    public function updateService(Request $r, Service $service) { $d=$r->validate(['name'=>'sometimes|required|string|max:255','description'=>'nullable|string','duration'=>'sometimes|required|integer|min:1','price'=>'sometimes|required|numeric|min:0','status'=>'sometimes|required|in:active,inactive']);$service->update($d);return response()->json(['success'=>true,'data'=>$service]); }
    public function deleteService(Service $service) { if($service->bookings()->exists()){$service->update(['status'=>'inactive']);return response()->json(['success'=>true,'message'=>'Layanan dinonaktifkan karena memiliki riwayat booking.','data'=>$service]);}$service->delete();return response()->json(null,204); }
    public function customers() { return response()->json(['success'=>true,'data'=>Customer::with('user')->withCount('vehicles')->latest()->paginate(20)]); }
    public function showCustomer(Customer $customer) { return response()->json(['success'=>true,'data'=>$customer->load(['user','vehicles','bookings.service'])]); }
    public function vehicles() { return response()->json(['success'=>true,'data'=>Vehicle::with('customer.user')->latest()->paginate(20)]); }
    public function showVehicle(Vehicle $vehicle) { return response()->json(['success'=>true,'data'=>$vehicle->load('customer.user')]); }
    public function records() { return response()->json(['success'=>true,'data'=>ServiceRecord::with(['booking.customer.user','vehicle','service'])->latest()->paginate(20)]); }
    public function storeRecord(Request $r) {
        $d=$r->validate(['booking_id'=>'required|exists:bookings,id|unique:service_records,booking_id','inspection'=>'nullable|string','work_done'=>'nullable|string','notes'=>'nullable|string','service_date'=>'nullable|date']);
        $booking=Booking::where('status','confirmed')->findOrFail($d['booking_id']);return response()->json(['success'=>true,'data'=>ServiceRecord::create($d+['vehicle_id'=>$booking->vehicle_id,'service_id'=>$booking->service_id,'status'=>'waiting'])],201);
    }
    public function updateRecord(Request $r, ServiceRecord $serviceRecord) { $d=$r->validate(['inspection'=>'nullable|string','work_done'=>'nullable|string','notes'=>'nullable|string','service_date'=>'nullable|date']);$serviceRecord->update($d);return response()->json(['success'=>true,'data'=>$serviceRecord]); }
    public function changeRecordStatus(Request $r, ServiceRecord $serviceRecord) {
        $d=$r->validate(['status'=>'required|in:waiting,inspection,processing,completed,picked_up']);$flow=['waiting'=>'inspection','inspection'=>'processing','processing'=>'completed','completed'=>'picked_up'];
        if(($flow[$serviceRecord->status]??null)!==$d['status'])throw ValidationException::withMessages(['status'=>['Perubahan status servis tidak valid.']]);
        DB::transaction(function()use($serviceRecord,$d){$serviceRecord->update($d);if($d['status']==='completed')$serviceRecord->update(['service_date'=>$serviceRecord->service_date??today()]);});return response()->json(['success'=>true,'data'=>$serviceRecord->fresh()]);
    }
    public function testimonials() { return response()->json(['success'=>true,'data'=>Testimonial::with(['customer.user','booking.vehicle','booking.service'])->latest()->paginate(20)]); }
    public function showTestimonial(Testimonial $testimonial) { return response()->json(['success'=>true,'data'=>$testimonial->load(['customer.user','booking.vehicle','booking.service'])]); }
    public function changeTestimonialStatus(Request $r, Testimonial $testimonial) { $d=$r->validate(['status'=>'required|in:published,hidden']);$testimonial->update($d);return response()->json(['success'=>true,'data'=>$testimonial]); }
}
