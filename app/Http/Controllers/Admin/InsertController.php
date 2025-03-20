<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class InsertController extends Controller
{
    public function showForm()
    {
        // Fetch the users or any specific data you want to show in the form
        $users = User::all(); // You can adjust this query as needed (e.g., limit, filter, etc.)
        
        // Pass the $users variable to the view
        return view('admin.insert', compact('users'));
    }

    public function insertTransaction(Request $request)
    {
        // Validate form input
        $request->validate([
            'norek' => 'required|exists:users,rek', // Ensure rekening exists in the users table
            'jumlah_transaksi' => 'required|numeric',
            'deskripsi' => 'required|string',
            'status_hutang' => 'required|string',
            'tanggal_transaksi' => 'required|date',
        ]);

        // Extract data from the request
        $norek = $request->input('norek');
        $jumlah_transaksi = str_replace(['.', ' '], '', $request->input('jumlah_transaksi')); // Clean up the currency format
        $jumlah_transaksi = intval($jumlah_transaksi); // Convert to integer
        $deskripsi = $request->input('deskripsi');
        $status_hutang = $request->input('status_hutang');
        $tanggal_transaksi = $request->input('tanggal_transaksi');
        $current_time = now()->format('H:i:s'); // Get current time
        $tanggal_transaksi_full = $tanggal_transaksi . ' ' . $current_time;

        // Fetch user using rekening (rek) value
        $user = User::where('rek', $norek)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['norek' => 'Nomor rekening tidak valid atau admin tidak dapat dipilih!']);
        }

        // Begin the transaction
        DB::beginTransaction();
        try {
            // Insert data into daftar_hutang
            DaftarHutang::create([
                'user_id' => $user->id, // user_id is from the users table based on rekening
                'jumlah_transaksi' => $jumlah_transaksi,
                'deskripsi' => $deskripsi,
                'status_hutang' => $status_hutang,
                'tanggal_transaksi' => $tanggal_transaksi_full, // Passing the full datetime string
            ]);

            // Commit the transaction
            DB::commit();

            // Success message
            return redirect()->route('admin.insert.form')->with('success', 'Transaksi berhasil ditambahkan untuk ' . $user->email);
        } catch (\Exception $e) {
            // Rollback the transaction if there's an error
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}

