<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\Gudang;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RakController extends Controller
{
    public function rakDibeli()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(403, 'User tidak ditemukan.');
            }

            $transactions = Transaction::where('user_id', $user->id)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->with(['rak.gudang'])
                ->orderBy('created_at', 'desc')
                ->get();

            $rakIds = $transactions->pluck('rak_id')->unique();

            $raks = Rak::whereIn('id', $rakIds)
                ->with('gudang')
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            $raks->getCollection()->transform(function ($rak) use ($transactions) {
                $transaction = $transactions->where('rak_id', $rak->id)->first();

                if ($transaction) {
                    $rak->transaction_date = $transaction->created_at;
                    $rak->order_id = $transaction->order_id;
                    $rak->payment_type = $transaction->payment_type;
                    $rak->transaction_id = $transaction->id;
                }

                return $rak;
            });

            return view('customer.list-rak.rak', compact('raks'));
        } catch (\Exception $e) {
            \Log::error('Error in rakDibeli: ' . $e->getMessage());

            $raks = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9, 1);
            return view('customer.list-rak.rak', compact('raks'));
        }
    }

    public function detailRak($id)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'User tidak ditemukan.');
        }

        $transaction = Transaction::where('user_id', $user->id)
            ->where('rak_id', $id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->with('rak.gudang')
            ->first();

        if (!$transaction) {
            return redirect()->route('customer.list-rak.rak')
                ->with('error', 'Anda belum membeli rak ini atau transaksi belum berhasil.');
        }

        $rak = $transaction->rak;
        $rak->transaction = $transaction;
        $rak->transaction_date = $transaction->created_at;
        $rak->order_id = $transaction->order_id;
        $rak->payment_type = $transaction->payment_type;

        // Tambahkan status masa sewa
        $rak->status_sewa = $transaction->status_sewa;
        $rak->sisa_hari = $transaction->sisa_hari;


        return view('customer.list-rak.show', compact('rak'));
    }

    private function userMemilikiRak($rakId, $user)
    {
        return Transaction::where('user_id', $user->id)
            ->where('rak_id', $rakId)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->exists();
    }

    public function riwayatRak($id)
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)
            ->where('rak_id', $id)
            ->with('rak')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('customer.list-rak.rak')
                ->with('error', 'Tidak ada riwayat transaksi untuk rak ini.');
        }

        $rak = Rak::findOrFail($id);

        return view('customer.list-rak.riwayat', compact('transactions', 'rak'));
    }

    public function index()
    {
        $raks = Rak::latest()->paginate(10);
        return view('admin.raks.index', compact('raks'));
    }

    public function create()
    {
        $gudangs = Gudang::where('is_active', true)
            ->orderBy('nama_gudang')
            ->get();

        return view('admin.raks.create', compact('gudangs'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'harga_sewa_perbulan' => str_replace('.', '', $request->harga_sewa_perbulan)
        ]);

        $validated = $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak',
            'nama_rak' => 'required|string|max:255',
            'jenis_rak' => 'required|in:Heavy Duty,Medium Duty,Light Duty,Pallet Rack,Cantilever',
            'deskripsi' => 'nullable|string',
            'kapasitas_berat' => 'required|integer|min:0',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'jumlah_tingkat' => 'required|integer|min:1',
            'lokasi_gudang' => 'required|string|max:255',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean',
            'durasi_sewa_hari' => 'required|integer|min:1'

        ]);

        $validated['status'] = 'tersedia';

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_rak) . '.' . $foto->getClientOriginalExtension();
            $validated['foto'] = $foto->storeAs('raks', $filename, 'public');
        }

        Rak::create($validated);

        return redirect()->route('raks.index')
            ->with('success', 'Rak berhasil ditambahkan dengan status Tersedia!');
    }

    public function show(Rak $rak)
    {
        return view('admin.raks.show', compact('rak'));
    }

    public function edit(Rak $rak)
    {
        $gudangs = Gudang::where('is_active', true)
            ->orderBy('nama_gudang')
            ->get();

        return view('admin.raks.edit', compact('rak', 'gudangs'));
    }

    public function update(Request $request, Rak $rak)
    {
        $request->merge([
            'harga_sewa_perbulan' => str_replace('.', '', $request->harga_sewa_perbulan)
        ]);

        $validated = $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak,' . $rak->id,
            'nama_rak' => 'required|string|max:255',
            'jenis_rak' => 'required|in:Heavy Duty,Medium Duty,Light Duty,Pallet Rack,Cantilever',
            'deskripsi' => 'nullable|string',
            'kapasitas_berat' => 'required|integer|min:0',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'jumlah_tingkat' => 'required|integer|min:1',
            'lokasi_gudang' => 'required|string|max:255',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,terisi,maintenance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean',
            'durasi_sewa_hari' => 'required|integer|min:1'
        ]);

        if ($request->hasFile('foto')) {
            if ($rak->foto && Storage::disk('public')->exists($rak->foto)) {
                Storage::disk('public')->delete($rak->foto);
            }

            $foto = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_rak) . '.' . $foto->getClientOriginalExtension();
            $validated['foto'] = $foto->storeAs('raks', $filename, 'public');
        }

        $rak->update($validated);

        return redirect()->route('raks.index')
            ->with('success', 'Rak berhasil diperbarui!');
    }

    public function destroy(Rak $rak)
    {
        // Cek apakah rak sudah terisi (ada transaksi yang berhasil)
        $hasActiveTransaction = Transaction::where('rak_id', $rak->id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->exists();

        if ($hasActiveTransaction) {
            return redirect()->route('raks.index')
                ->with('error', 'Rak tidak dapat dihapus karena sedang terisi/disewa oleh customer!');
        }

        // Cek berdasarkan status rak
        if ($rak->status === 'terisi') {
            return redirect()->route('raks.index')
                ->with('error', 'Rak tidak dapat dihapus karena statusnya masih terisi!');
        }

        // Hapus foto jika ada
        if ($rak->foto && Storage::disk('public')->exists($rak->foto)) {
            Storage::disk('public')->delete($rak->foto);
        }

        $rak->delete();

        return redirect()->route('raks.index')
            ->with('success', 'Rak berhasil dihapus!');
    }
}
