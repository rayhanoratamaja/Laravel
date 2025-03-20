<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Ambil data pengguna yang sedang login
        $user = Auth::user();

        // Format saldo menjadi IDR dan hilangkan desimal jika saldo bulat
        $formattedSaldo = 'Rp. ' . number_format($user->saldo, 0, ',', '.');

        // Hitung sisa utang
        $totalUtang = 0;
        $totalPembayaran = 0;

        // Ambil semua transaksi utang yang relevan untuk pengguna
        $transactions = DB::table('daftar_hutang')
            ->where('user_id', $user->rek)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Proses transaksi untuk menghitung sisa utang
        foreach ($transactions as $transaction) {
            $jumlahTransaksi = floatval($transaction->jumlah_transaksi);
            $statusHutang = $transaction->status_hutang;

            // Hanya sertakan transaksi yang memiliki status selain "belum_diterima", "info", dan "tolak"
            if (!in_array($statusHutang, ['belum_diterima', 'info', 'tolak'])) {
                if ($jumlahTransaksi > 0) {
                    // Menambah utang jika jumlah_transaksi positif
                    $totalUtang += $jumlahTransaksi;
                } elseif ($jumlahTransaksi < 0) {
                    // Mengurangi utang jika jumlah_transaksi negatif (pembayaran)
                    $totalPembayaran += abs($jumlahTransaksi); // Menggunakan abs() untuk mengambil nilai absolut
                }
            }
        }

        // Hitung total sisa utang
        $sisaUtang = $totalUtang - $totalPembayaran;
        $formattedSisaUtang = 'Rp. ' . number_format($sisaUtang, 0, ',', '.');

        // Pass data ke view
        return view('home', [
            'userName' => $user->name,
            'saldo' => $formattedSaldo,
            'user_id' => $user->user_id,
            'sisaUtang' => $formattedSisaUtang,
            'transactions' => $transactions, // Pass transaksi ke view
        ]);
    }
}
