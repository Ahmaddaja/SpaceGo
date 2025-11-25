<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rak;

class CustomerController extends Controller
{
    // Menampilkan semua customer
    public function index()
    {
        $customers = User::where('role', 'customer')->get();

        return view('admin.pelanggan.pelanggan', compact('customers'));
    }

     public function listRak(Request $request)
    {
        // Ambil semua jenis rak unik dari database
        $jenisList = Rak::select('jenis_rak')->distinct()->pluck('jenis_rak');

        // Query dasar
        $query = Rak::with('gudang');

        // FILTER SEARCH
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_rak', 'like', "%{$request->search}%")
                ->orWhere('nama_rak', 'like', "%{$request->search}%");
            });
        }

        // FILTER JENIS RAK
        if ($request->jenis) {
            $query->where('jenis_rak', $request->jenis);
        }

        $raks = $query->get();

        return view('customer.list-rak.list-rak', compact('raks', 'jenisList'));
    }

    public function showRak($id)
    {
        $rak = Rak::with('gudang')->findOrFail($id);

        return view('customer.list-rak.show', compact('rak'));
    }

    public function dashboard()
    {
        return view('customer.index');
    }

    // TAMBAHKAN METHOD BAYAR INI
    public function bayar($id)
    {
        $rak = Rak::with('gudang')->findOrFail($id);
        
        // Validasi ketersediaan rak
        if ($rak->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Rak tidak tersedia untuk disewa.');
        }

        return view('customer.bayar', compact('rak'));
    }
}