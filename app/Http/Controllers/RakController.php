<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RakController extends Controller
{
    public function index()
    {
        $raks = Rak::latest()->paginate(10);
        return view('admin.raks.index', compact('raks'));
    }

    public function create()
    {
        return view('admin.raks.create');
    }

    public function store(Request $request)
    {
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
        return view('admin.raks.edit', compact('rak'));
    }

    public function update(Request $request, Rak $rak)
    {
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
