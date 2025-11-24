<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGudang = Gudang::count();
        $totalPelanggan = User::where('role', 'customer')->count();

        return view('admin.dashboard', compact('totalGudang', 'totalPelanggan'));
    }
}
