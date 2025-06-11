<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ExternalAuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $response = Http::post('https://gisapis.manpits.xyz/api/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // dd($data);

            // Cek apakah token tersedia
            if (!isset($data['meta']['token'])) {
                return back()->withErrors(['email' => 'Login gagal: token tidak ditemukan dalam response API.']);
            }

            // Simpan token di sesi
            Session::put('api_token', $data['meta']['token']);

            // Simpan/Update user lokal dan login
            $user = User::updateOrCreate(
                ['email' => $request->email],
                ['name' => $request->email, 'password' => Hash::make(uniqid())]
            );
            Auth::login($user);

            return redirect()->intended('/locations');
        } else {
            return back()->withErrors(['email' => 'Login gagal: ' . $response->json('meta.message') ?? 'Terjadi kesalahan']);
        }
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $response = Http::post('https://gisapis.manpits.xyz/api/register', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('status', 'Registrasi berhasil, silakan login');
        } else {
            return back()->withErrors(['email' => 'Registrasi gagal: ' . $response->json('message')]);
        }
    }

    public function logout(Request $request)
    {
        if (Session::has('api_token')) {
            Http::withToken(Session::get('api_token'))->post('https://gisapis.manpits.xyz/api/logout');
        }

        Session::forget('api_token');
        Auth::logout();
        return redirect('/login');
    }
}

