<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Customer, Service, ServiceRecord, Testimonial, User, Vehicle};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Validation\ValidationException;

class AutoCareController extends Controller
{
    public function home() { return view('public.home', ['services'=>Service::where('status','active')->limit(3)->get(), 'testimonials'=>Testimonial::where('status','published')->with(['customer.user','booking.vehicle'])->latest()->limit(3)->get()]); }
    public function services() { return view('public.services', ['services'=>Service::where('status','active')->orderBy('name')->paginate(9)]); }
    public function service(Service $service) { abort_unless($service->status==='active',404); return view('public.service', compact('service')); }
    public function testimonials() { return view('public.testimonials', ['testimonials'=>Testimonial::where('status','published')->with(['customer.user','booking.vehicle'])->latest()->paginate(12)]); }
    public function contact() { return view('public.contact'); }

    public function loginForm() { return view('auth.login'); }
    public function login(Request $request) {
        $credentials=$request->validate(['email'=>'required|email','password'=>'required|string']);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email');
        $request->session()->regenerate(); return redirect()->route(Auth::user()->role==='admin'?'admin.dashboard':'customer.dashboard');
    }
    public function registerForm() { return view('auth.register'); }
    public function register(Request $request) {
        $d=$request->validate(['name'=>'required|string|max:255','email'=>'required|email|max:255|unique:users,email','password'=>'required|string|min:8|confirmed','phone'=>'nullable|string|max:30','address'=>'nullable|string']);
        $user=DB::transaction(function()use($d){$u=User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>$d['password'],'role'=>'customer']);$u->customer()->create(['phone'=>$d['phone']??null,'address'=>$d['address']??null]);return $u;});
        Auth::login($user);$request->session()->regenerate();return redirect()->route('customer.dashboard')->with('success','Akun berhasil dibuat. Selamat datang di AutoCare.');
    }
    public function logout(Request $request) { Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('home'); }

    private function customer(Request $r): Customer { return $r->user()->customer()->firstOrFail(); }
    public function customerDashboard(Request $r) { $c=$this->customer($r);return view('customer.dashboard',['customer'=>$c,'vehicles'=>$c->vehicles()->count(),'bookings'=>$c->bookings()->whereIn('status',['pending','confirmed'])->count(),'history'=>ServiceRecord::whereHas('booking',fn($q)=>$q->where('customer_id',$c->id))->count(),'recent'=>$c->bookings()->with(['vehicle','service'])->latest()->limit(5)->get()]); }
    public function profile(Request $r) { return view('customer.profile',['user'=>$r->user(),'customer'=>$this->customer($r)]); }
    public function updateProfile(Request $r) { $d=$r->validate(['name'=>'required|string|max:255','phone'=>'nullable|string|max:30','address'=>'nullable|string']);DB::transaction(function()use($r,$d){$r->user()->update(['name'=>$d['name']]);$this->customer($r)->update(['phone'=>$d['phone']??null,'address'=>$d['address']??null]);});return back()->with('success','Profil berhasil diperbarui.'); }
    public function vehicles(Request $r) { return view('customer.vehicles.index',['vehicles'=>$this->customer($r)->vehicles()->latest()->paginate(12)]); }
    public function vehicleCreate() { return view('customer.vehicles.form',['vehicle'=>new Vehicle,'action'=>route('customer.vehicles.store'),'method'=>'POST']); }
    public function vehicleStore(Request $r) { $d=$r->validate(['brand'=>'required|string|max:100','model'=>'required|string|max:100','license_plate'=>'required|string|max:30|unique:vehicles,license_plate','year'=>'required|integer|min:1900|max:'.(date('Y')+1)]);$this->customer($r)->vehicles()->create($d);return redirect()->route('customer.vehicles.index')->with('success','Kendaraan berhasil ditambahkan.'); }
    public function vehicleEdit(Request $r, Vehicle $vehicle) { $this->assertOwner($r,$vehicle);return view('customer.vehicles.form',['vehicle'=>$vehicle,'action'=>route('customer.vehicles.update',$vehicle),'method'=>'PUT']); }
    public function vehicleUpdate(Request $r, Vehicle $vehicle) { $this->assertOwner($r,$vehicle);$d=$r->validate(['brand'=>'required|string|max:100','model'=>'required|string|max:100','license_plate'=>'required|string|max:30|unique:vehicles,license_plate,'.$vehicle->id,'year'=>'required|integer|min:1900|max:'.(date('Y')+1)]);$vehicle->update($d);return redirect()->route('customer.vehicles.index')->with('success','Kendaraan berhasil diperbarui.'); }
    public function vehicleDelete(Request $r, Vehicle $vehicle) { $this->assertOwner($r,$vehicle);if($vehicle->bookings()->exists())return back()->with('error','Kendaraan yang sudah memiliki booking tidak dapat dihapus.');$vehicle->delete();return back()->with('success','Kendaraan berhasil dihapus.'); }
    private function assertOwner(Request $r, Vehicle $v): void { abort_unless($v->customer_id===$this->customer($r)->id,404); }

    public function bookings(Request $r) { return view('customer.bookings.index',['bookings'=>$this->customer($r)->bookings()->with(['vehicle','service','serviceRecord'])->latest()->paginate(12)]); }
    public function bookingCreate(Request $r) { return view('customer.bookings.create',['vehicles'=>$this->customer($r)->vehicles,'services'=>Service::where('status','active')->orderBy('name')->get(),'selectedService'=>Service::where('status','active')->whereKey($r->query('service'))->value('id')]); }
    public function bookingStore(Request $r) {
        $d=$r->validate(['vehicle_id'=>'required|integer','service_id'=>'required|exists:services,id','booking_date'=>'required|date|after_or_equal:today','booking_time'=>'required|date_format:H:i','complaint'=>'nullable|string|max:2000']);$c=$this->customer($r);
        $b=DB::transaction(function()use($d,$c){$vehicle=$c->vehicles()->findOrFail($d['vehicle_id']);$service=Service::where('status','active')->findOrFail($d['service_id']);$start=\Illuminate\Support\Carbon::parse($d['booking_date'].' '.$d['booking_time']);$end=$start->copy()->addMinutes($service->duration);
            $busy=Booking::whereDate('booking_date',$d['booking_date'])->whereIn('status',['pending','confirmed'])->with('service')->lockForUpdate()->get()->contains(function($x)use($start,$end){$s=\Illuminate\Support\Carbon::parse($x->booking_date->format('Y-m-d').' '.$x->booking_time);return $start->lt($s->copy()->addMinutes($x->service->duration))&&$s->lt($end);});
            if($busy)throw ValidationException::withMessages(['booking_time'=>'Waktu tersebut baru saja dipesan. Silakan pilih waktu lain.']);
            return $c->bookings()->create(['vehicle_id'=>$vehicle->id,'service_id'=>$service->id,'booking_date'=>$d['booking_date'],'booking_time'=>$d['booking_time'],'complaint'=>$d['complaint']??null,'status'=>'pending']);});
        return redirect()->route('customer.bookings.success',$b)->with('success','Booking berhasil dibuat dan menunggu konfirmasi bengkel.');
    }
    public function bookingShow(Request $r, Booking $booking) { abort_unless($booking->customer_id===$this->customer($r)->id,404);return view('customer.bookings.show',['booking'=>$booking->load(['vehicle','service','serviceRecord'])]); }
    public function bookingSuccess(Request $r, Booking $booking) { abort_unless($booking->customer_id===$this->customer($r)->id,404);return view('customer.bookings.success',['booking'=>$booking->load(['vehicle','service'])]); }
    public function history(Request $r) { return view('customer.history.index',['records'=>ServiceRecord::whereHas('booking',fn($q)=>$q->where('customer_id',$this->customer($r)->id))->with(['vehicle','service','booking'])->latest()->paginate(12)]); }
    public function historyShow(Request $r, ServiceRecord $serviceRecord) { abort_unless($serviceRecord->booking()->where('customer_id',$this->customer($r)->id)->exists(),404);return view('customer.history.show',['record'=>$serviceRecord->load(['vehicle','service','booking'])]); }
    public function ownTestimonials(Request $r) { return view('customer.testimonials.index',['testimonials'=>$this->customer($r)->testimonials()->with('booking.service')->latest()->paginate(12),'eligible'=>$this->customer($r)->bookings()->whereHas('serviceRecord',fn($q)=>$q->whereIn('status',['completed','picked_up']))->whereDoesntHave('testimonial')->with(['vehicle','service'])->get()]); }
    public function testimonialStore(Request $r) { $d=$r->validate(['booking_id'=>'required|integer','rating'=>'required|integer|between:1,5','comment'=>'required|string|max:3000']);$c=$this->customer($r);$b=$c->bookings()->whereHas('serviceRecord',fn($q)=>$q->whereIn('status',['completed','picked_up']))->whereDoesntHave('testimonial')->findOrFail($d['booking_id']);$c->testimonials()->create($d+['status'=>'pending']);return back()->with('success','Testimoni berhasil dikirim untuk ditinjau.'); }

    public function adminDashboard() { return view('admin.dashboard',['stats'=>['total_bookings'=>Booking::count(),'today_bookings'=>Booking::whereDate('booking_date',today())->count(),'customers'=>Customer::count(),'vehicles'=>Vehicle::count(),'records'=>ServiceRecord::count(),'testimonials'=>Testimonial::count(),'rating'=>round(Testimonial::where('status','published')->avg('rating')??0,1)],'recent'=>Booking::with(['customer.user','vehicle','service'])->latest()->limit(8)->get()]); }
    public function adminBookings() { return view('admin.bookings.index',['bookings'=>Booking::with(['customer.user','vehicle','service','serviceRecord'])->latest()->paginate(20)]); }
    public function adminBooking(Booking $booking) { return view('admin.bookings.show',['booking'=>$booking->load(['customer.user','vehicle','service','serviceRecord'])]); }
    public function adminBookingStatus(Request $r,Booking $booking) { $d=$r->validate(['status'=>'required|in:confirmed,cancelled']);if($booking->status!=='pending')return back()->with('error','Status booking ini sudah tidak dapat diubah.');$booking->update($d);return back()->with('success','Status booking diperbarui.'); }
    public function adminServices() { return view('admin.services.index',['services'=>Service::latest()->paginate(20)]); }
    public function adminServiceCreate() { return view('admin.services.form',['service'=>new Service,'action'=>route('admin.services.store'),'method'=>'POST']); }
    public function adminServiceStore(Request $r) { $d=$r->validate(['name'=>'required|string|max:255','description'=>'nullable|string','duration'=>'required|integer|min:1','price'=>'required|numeric|min:0','status'=>'required|in:active,inactive']);Service::create($d);return redirect()->route('admin.services.index')->with('success','Layanan ditambahkan.'); }
    public function adminServiceEdit(Service $service) { return view('admin.services.form',['service'=>$service,'action'=>route('admin.services.update',$service),'method'=>'PUT']); }
    public function adminServiceUpdate(Request $r,Service $service) { $d=$r->validate(['name'=>'required|string|max:255','description'=>'nullable|string','duration'=>'required|integer|min:1','price'=>'required|numeric|min:0','status'=>'required|in:active,inactive']);$service->update($d);return redirect()->route('admin.services.index')->with('success','Layanan diperbarui.'); }
    public function adminServiceDelete(Service $service) { if($service->bookings()->exists())$service->update(['status'=>'inactive']);else $service->delete();return back()->with('success','Layanan dihapus atau dinonaktifkan karena memiliki riwayat.'); }
    public function adminCustomers() { return view('admin.customers.index',['customers'=>Customer::with('user')->withCount('vehicles')->latest()->paginate(20)]); }
    public function adminCustomer(Customer $customer) { return view('admin.customers.show',['customer'=>$customer->load(['user','vehicles','bookings.service'])]); }
    public function adminVehicles() { return view('admin.vehicles.index',['vehicles'=>Vehicle::with('customer.user')->latest()->paginate(20)]); }
    public function adminRecords() { return view('admin.records.index',['records'=>ServiceRecord::with(['booking.customer.user','vehicle','service'])->latest()->paginate(20)]); }
    public function adminRecordCreate() { $bookings=Booking::where('status','confirmed')->whereDoesntHave('serviceRecord')->with(['customer.user','vehicle','service'])->get();return view('admin.records.form',['record'=>new ServiceRecord,'bookings'=>$bookings]); }
    public function adminRecordStore(Request $r) { $d=$r->validate(['booking_id'=>'required|exists:bookings,id|unique:service_records,booking_id','inspection'=>'nullable|string','work_done'=>'nullable|string','notes'=>'nullable|string','service_date'=>'nullable|date']);$b=Booking::where('status','confirmed')->findOrFail($d['booking_id']);ServiceRecord::create($d+['vehicle_id'=>$b->vehicle_id,'service_id'=>$b->service_id,'status'=>'waiting']);return redirect()->route('admin.records.index')->with('success','Catatan servis dibuat.'); }
    public function adminRecordEdit(ServiceRecord $serviceRecord) { return view('admin.records.form',['record'=>$serviceRecord,'bookings'=>collect()]); }
    public function adminRecordUpdate(Request $r,ServiceRecord $serviceRecord) { $d=$r->validate(['inspection'=>'nullable|string','work_done'=>'nullable|string','notes'=>'nullable|string','service_date'=>'nullable|date']);$serviceRecord->update($d);return back()->with('success','Catatan servis diperbarui.'); }
    public function adminRecordStatus(Request $r,ServiceRecord $serviceRecord) { $d=$r->validate(['status'=>'required|in:waiting,inspection,processing,completed,picked_up']);$next=['waiting'=>'inspection','inspection'=>'processing','processing'=>'completed','completed'=>'picked_up'];if(($next[$serviceRecord->status]??null)!==$d['status'])return back()->with('error','Transisi status tidak valid.');$serviceRecord->update($d+($d['status']==='completed'?['service_date'=>$serviceRecord->service_date??today()]:[]));return back()->with('success','Status servis diperbarui.'); }
    public function adminTestimonials() { return view('admin.testimonials.index',['testimonials'=>Testimonial::with(['customer.user','booking.vehicle','booking.service'])->latest()->paginate(20)]); }
    public function adminTestimonialStatus(Request $r,Testimonial $testimonial) { $d=$r->validate(['status'=>'required|in:published,hidden']);$testimonial->update($d);return back()->with('success','Status testimoni diperbarui.'); }
    public function adminProfile(Request $r) { return view('admin.profile',['user'=>$r->user()]); }
    public function adminProfileUpdate(Request $r) { $d=$r->validate(['name'=>'required|string|max:255']);$r->user()->update($d);return back()->with('success','Profil admin diperbarui.'); }
}
