<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','email'=>'required|email|max:255|unique:users,email','password'=>'required|string|min:8|confirmed','phone'=>'nullable|string|max:30','address'=>'nullable|string']);
        $user = DB::transaction(function () use ($data) {
            $user = User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>$data['password'],'role'=>'customer']);
            $user->customer()->create(['phone'=>$data['phone'] ?? null,'address'=>$data['address'] ?? null]);
            return $user;
        });
        return response()->json(['success'=>true,'message'=>'Registrasi berhasil.','data'=>['user'=>$user->load('customer'),'token'=>$user->createToken('autocare')->plainTextToken]], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email'=>'required|email','password'=>'required|string']);
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) throw ValidationException::withMessages(['email'=>['Email atau password salah.']]);
        return response()->json(['success'=>true,'message'=>'Login berhasil.','data'=>['user'=>$user->load('customer'),'token'=>$user->createToken('autocare')->plainTextToken]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['success'=>true,'message'=>'Logout berhasil.']);
    }
}
