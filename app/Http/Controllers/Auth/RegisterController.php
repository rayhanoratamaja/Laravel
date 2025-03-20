<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationSuccessMail; // Sesuaikan dengan mail yang Anda buat

class RegisterController extends Controller
{
    // Menampilkan form registrasi
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Menangani proses registrasi
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Menyimpan pengguna baru ke database dengan status_verif = 0 (belum terverifikasi)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status_verif' => 0, // Status verifikasi email di-set ke 0
        ]);

        // Kirim email konfirmasi akun sudah terdaftar
        Mail::to($user->email)->send(new RegistrationSuccessMail($user));

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Arahkan ke halaman login dengan status
        return redirect()->route('login')->with('status', 'Akunmu udah Terdaftar tetapi akun mu belum dapat digunakan :( , akun mu baru bisa digunakan setelah admin menyetujui permintaan anda selengkap nya anda dapat memeriksa email yang kamu daftarkan');
    }
}
