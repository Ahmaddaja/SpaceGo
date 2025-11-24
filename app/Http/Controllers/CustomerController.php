<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    // Menampilkan semua customer
    public function index()
    {
        $customers = User::where('role', 'customer')->get();

        return view('admin.pelanggan.pelanggan', compact('customers'));
    }

    // Detail customer
}
