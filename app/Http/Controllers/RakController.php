<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Gudang;
use App\Models\User;

class RakController extends Controller
{
    public function rakDibeli()
    {
        // Ambil user yang sedang login
        $user = Auth::user();
        
        // Debug untuk melihat user
        if (!$user) {
            abort(403, 'User tidak ditemukan.');
        }
        
        // Asumsi ada relasi 'rakDibeli' di model User
        // Jika belum ada relasi, kita bisa query manual dulu
        try {
            // Coba dengan relasi jika ada
            if (method_exists($user, 'rakDibeli')) {
                $raks = $user->rakDibeli()
                            ->with('gudang')
                            ->orderBy('created_at', 'desc')
                            ->paginate(9);
            } else {
                // Fallback: query manual (sesuaikan dengan struktur database kamu)
                $raks = \App\Models\Rak::where('user_id', $user->id) // atau sesuaikan dengan kolom yang tepat
                            ->with('gudang')
                            ->orderBy('created_at', 'desc')
                            ->paginate(9);
            }
            
            return view('customer.list-rak.rak', compact('raks'));
            
        } catch (\Exception $e) {
            // Fallback jika ada error
            $raks = collect(); // empty collection
            return view('customer.list-rak.rak', compact('raks'));
        }
    }

    public function detailRak($id)
    {
        $rak = \App\Models\Rak::with('gudang')->findOrFail($id);
        
        // Cek apakah user memiliki akses ke rak ini
        $user = Auth::user();
        if (!$this->userMemilikiRak($rak->id, $user)) {
            abort(403, 'Anda tidak memiliki akses ke rak ini.');
        }
        
        return view('customer.detail-rak', compact('rak'));
    }
    
    private function userMemilikiRak($rakId, $user)
    {
        // Sesuaikan dengan logika bisnis kamu
        // Contoh sederhana: cek apakah rak memiliki user_id yang sama
        $rak = \App\Models\Rak::find($rakId);
        return $rak && $rak->user_id == $user->id;
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
            // 'zona_gudang' => 'nullable|string|max:50',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            // Hapus validasi status untuk create
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Status otomatis "tersedia" untuk rak baru
        $validated['status'] = 'tersedia';

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_rak) . '.' . $foto->getClientOriginalExtension();
            $validated['foto'] = $foto->storeAs('raks', $filename, 'public');
        }

        Rak::create($validated);

        return redirect()->route('raks.index')->with('success', 'Rak berhasil ditambahkan dengan status Tersedia!');
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
            // 'zona_gudang' => 'nullable|string|max:50',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,terisi,maintenance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean'
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

        return redirect()->route('raks.index')->with('success', 'Rak berhasil diperbarui!');
    }

    public function destroy(Rak $rak)
    {
        if ($rak->foto && Storage::disk('public')->exists($rak->foto)) {
            Storage::disk('public')->delete($rak->foto);
        }

        $rak->delete();

        return redirect()->route('raks.index')->with('success', 'Rak berhasil dihapus!');
    }

}
