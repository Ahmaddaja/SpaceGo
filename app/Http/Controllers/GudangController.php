<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Rak;

class GudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::latest()->withCount('raks')->paginate(10);

        // Load count manual untuk setiap gudang
        foreach ($gudangs as $gudang) {
            $gudang->raks_count = \App\Models\Rak::where('lokasi_gudang', $gudang->nama_gudang)->count();
        }

        return view('admin.gudangs.index', compact('gudangs'));
    }

    public function create()
    {
        return view('admin.gudangs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_gudang' => 'required|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_gudang) . '.' . $foto->getClientOriginalExtension();
            $validated['foto'] = $foto->storeAs('gudangs', $filename, 'public');
        }

        Gudang::create($validated);

        return redirect()->route('gudangs.index')->with('success', 'Gudang berhasil ditambahkan!');
    }

    public function show(Gudang $gudang)
    {

        $raks = $gudang->raks;
        $gudang->loadCount('raks');

        return view('admin.gudangs.show', compact('gudang', 'raks'));
    }
    
    public function edit(Gudang $gudang)
    {
        return view('admin.gudangs.edit', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $validated = $request->validate([
            'kode_gudang' => 'required|unique:gudangs,kode_gudang,' . $gudang->id,
            'nama_gudang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Simpan nama gudang lama
        $namaGudangLama = $gudang->nama_gudang;

        if ($request->hasFile('foto')) {
            if ($gudang->foto && Storage::disk('public')->exists($gudang->foto)) {
                Storage::disk('public')->delete($gudang->foto);
            }
            $foto = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_gudang) . '.' . $foto->getClientOriginalExtension();
            $validated['foto'] = $foto->storeAs('gudangs', $filename, 'public');
        }

        $gudang->update($validated);

        // Update lokasi_gudang di semua rak jika nama gudang berubah
        if ($namaGudangLama !== $validated['nama_gudang']) {
            \App\Models\Rak::where('lokasi_gudang', $namaGudangLama)
                ->update(['lokasi_gudang' => $validated['nama_gudang']]);
        }

        return redirect()->route('gudangs.index')->with('success', 'Gudang berhasil diperbarui!');
    }

    public function destroy(Gudang $gudang)
    {
        if ($gudang->raks()->count() > 0) {
            return redirect()->route('gudangs.index')->with('error', 'Gudang tidak dapat dihapus karena masih memiliki rak terdaftar!');
        }

        if ($gudang->foto && Storage::disk('public')->exists($gudang->foto)) {
            Storage::disk('public')->delete($gudang->foto);
        }

        $gudang->delete();

        return redirect()->route('gudangs.index')->with('success', 'Gudang berhasil dihapus!');
    }
}
