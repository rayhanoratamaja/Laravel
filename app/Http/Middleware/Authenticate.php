<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek jika pengguna belum terverifikasi
        if (Auth::check() && Auth::user()->status_verif == 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'akunmu belum disetujui ! , silahkan periksa email anda. untuk lebih lanjut jika ada pertanyaan hubungi 6285158993141 Whatsapp');
        }

        return $next($request);
    }
}
