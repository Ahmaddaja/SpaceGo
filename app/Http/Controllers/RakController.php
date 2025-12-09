<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\Gudang;
use App\Models\Transaction;
use App\Models\FotoRak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RakController extends Controller
{
    const MAX_PHOTOS = 4;

    public function rakDibeli()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(403, 'User tidak ditemukan.');
            }

            // Ambil transaksi dengan status sukses
            $transactions = Transaction::where('user_id', $user->id)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->with(['rak.gudang', 'rak.fotos'])
                ->orderBy('created_at', 'desc')
                ->get();

            $rakIds = $transactions->pluck('rak_id')->unique();

            // Filter hanya rak yang statusnya 'terisi' atau 'pengosongan'
            $raks = Rak::whereIn('id', $rakIds)
                ->whereIn('status', ['terisi', 'pengosongan'])
                ->with(['gudang', 'fotos'])
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            $raks->getCollection()->transform(function ($rak) use ($transactions) {
                $transaction = $transactions->where('rak_id', $rak->id)
                    ->sortByDesc('sewa_berakhir')
                    ->first();

                if ($transaction) {
                    $rak->transaction_date = $transaction->created_at;
                    $rak->order_id = $transaction->order_id;
                    $rak->payment_type = $transaction->payment_type;
                    $rak->transaction_id = $transaction->id;
                    $rak->is_pengosongan = $transaction->is_pengosongan ?? false;
                    $rak->pengosongan_dimulai = $transaction->pengosongan_dimulai ?? null;
                    $rak->pengosongan_berakhir = $transaction->pengosongan_berakhir ?? null;
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

        // Ambil transaksi aktif terbaru
        $transaction = Transaction::where('user_id', $user->id)
            ->where('rak_id', $id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->with('rak.gudang', 'rak.fotos')
            ->orderBy('sewa_berakhir', 'desc')
            ->first();

        if (!$transaction) {
            return redirect()->route('customer.list-rak.rak')
                ->with('error', 'Anda belum membeli rak ini atau transaksi belum berhasil.');
        }

        $rak = $transaction->rak;

        // Cek apakah rak sudah tersedia (artinya masa pengosongan telah berakhir)
        if ($rak->status === 'tersedia') {
            return redirect()->route('customer.list-rak.rak')
                ->with('info', 'Masa sewa rak ini telah berakhir dan rak sudah kembali tersedia untuk disewa.');
        }

        $rak->transaction = $transaction;
        $rak->transaction_date = $transaction->created_at;
        $rak->order_id = $transaction->order_id;
        $rak->payment_type = $transaction->payment_type;
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
        $raks = Rak::with(['fotos', 'gudang'])->latest()->paginate(10);
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
            'lokasi_gudang' => 'required|exists:gudangs,nama_gudang',
            'nama_rak' => 'required|string|max:255',
            'jenis_rak' => 'required|in:Heavy Duty,Medium Duty,Light Duty,Cantilever',
            'deskripsi' => 'nullable|string',
            'kapasitas_berat' => 'required|integer|min:0',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'jumlah_tingkat' => 'required|integer|min:1',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            'fotos' => 'nullable|array|max:' . self::MAX_PHOTOS,
            'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean',
            'durasi_sewa_hari' => 'required|integer|min:1'
        ], [
            'fotos.max' => 'Maksimal ' . self::MAX_PHOTOS . ' foto yang dapat diupload.'
        ]);

        DB::beginTransaction();
        try {
            $gudang = Gudang::where('nama_gudang', $validated['lokasi_gudang'])->firstOrFail();
            $validated['gudang_id'] = $gudang->id;
            $validated['lokasi_gudang'] = $gudang->nama_gudang;
            $validated['status'] = 'tersedia';

            unset($validated['fotos']);

            $rak = Rak::create($validated);

            if ($request->hasFile('fotos')) {
                $this->handleMultiplePhotos($request->file('fotos'), $rak);
            }

            DB::commit();

            return redirect()->route('raks.index')
                ->with('success', 'Rak berhasil ditambahkan dengan status Tersedia!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating rak: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan rak: ' . $e->getMessage());
        }
    }

    public function show(Rak $rak)
    {
        $rak->load('fotos');
        return view('admin.raks.show', compact('rak'));
    }

    public function edit(Rak $rak)
    {
        $gudangs = Gudang::where('is_active', true)
            ->orderBy('nama_gudang')
            ->get();

        $rak->load('fotos');

        return view('admin.raks.edit', compact('rak', 'gudangs'));
    }

    public function update(Request $request, Rak $rak)
    {
        $request->merge([
            'harga_sewa_perbulan' => str_replace('.', '', $request->harga_sewa_perbulan)
        ]);

        $hasActiveTransaction = Transaction::where('rak_id', $rak->id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->exists();

        if ($rak->status === 'pengosongan') {
            $pengosonganTransaction = Transaction::where('rak_id', $rak->id)
                ->where('is_pengosongan', true)
                ->whereNotNull('pengosongan_berakhir')
                ->first();

            if ($pengosonganTransaction) {
                $now = Carbon::now();
                $pengosonganEnd = Carbon::parse($pengosonganTransaction->pengosongan_berakhir);

                if ($now->lessThan($pengosonganEnd)) {
                    $daysLeft = $now->diffInDays($pengosonganEnd);
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Rak sedang dalam masa pengosongan! Status tidak dapat diubah. Masa pengosongan berakhir dalam {$daysLeft} hari.");
                }
            }
        }

        if ($rak->status === 'terisi' && $request->status !== 'terisi' && $hasActiveTransaction) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Rak sedang terisi/disewa! Status tidak dapat diubah ke ' . $request->status . '. Tunggu hingga masa sewa berakhir.');
        }

        if ($hasActiveTransaction && !in_array($request->status, ['terisi', 'pengosongan'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Rak memiliki transaksi aktif! Status harus tetap "Terisi" atau "Pengosongan".');
        }

        $existingPhotosCount = $rak->fotos()->count();
        $photosToDelete = $request->has('delete_fotos') ? count($request->delete_fotos) : 0;
        $newPhotosCount = $request->hasFile('fotos') ? count($request->file('fotos')) : 0;
        $totalPhotosAfter = $existingPhotosCount - $photosToDelete + $newPhotosCount;

        if ($totalPhotosAfter > self::MAX_PHOTOS) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total foto tidak boleh lebih dari ' . self::MAX_PHOTOS . '. Saat ini: ' . $existingPhotosCount . ' foto, akan dihapus: ' . $photosToDelete . ', akan ditambah: ' . $newPhotosCount . '.');
        }

        $validated = $request->validate([
            'kode_rak' => 'required|unique:raks,kode_rak,' . $rak->id,
            'lokasi_gudang' => 'required|exists:gudangs,nama_gudang',
            'nama_rak' => 'required|string|max:255',
            'jenis_rak' => 'required|in:Heavy Duty,Medium Duty,Light Duty,Cantilever',
            'deskripsi' => 'nullable|string',
            'kapasitas_berat' => 'required|integer|min:0',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'jumlah_tingkat' => 'required|integer|min:1',
            'harga_sewa_perbulan' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,terisi,maintenance,pengosongan',
            'fotos' => 'nullable|array',
            'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_primary' => 'nullable|integer',
            'delete_fotos' => 'nullable|array',
            'delete_fotos.*' => 'integer|exists:foto_rak,id',
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean',
            'durasi_sewa_hari' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $gudang = Gudang::where('nama_gudang', $validated['lokasi_gudang'])->first();
            if (!$gudang) {
                return redirect()->back()->withErrors(['lokasi_gudang' => 'Gudang tidak ditemukan.']);
            }

            $validated['gudang_id'] = $gudang->id;
            $validated['lokasi_gudang'] = $gudang->nama_gudang;

            unset($validated['fotos'], $validated['foto_primary'], $validated['delete_fotos']);

            $rak->update($validated);

            if ($request->has('delete_fotos')) {
                $this->deletePhotos($request->delete_fotos, $rak->id);
            }

            if ($request->hasFile('fotos')) {
                $this->handleMultiplePhotos($request->file('fotos'), $rak);
            }

            if ($request->has('foto_primary')) {
                $this->updatePrimaryPhoto($request->foto_primary, $rak->id);
            }

            DB::commit();

            return redirect()->route('raks.index')
                ->with('success', 'Rak berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating rak: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui rak: ' . $e->getMessage());
        }
    }

    public function destroy(Rak $rak)
    {
        $hasActiveTransaction = Transaction::where('rak_id', $rak->id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->exists();

        if ($hasActiveTransaction) {
            return redirect()->route('raks.index')
                ->with('error', 'Rak tidak dapat dihapus karena sedang terisi/disewa oleh customer!');
        }

        if (in_array($rak->status, ['terisi', 'pengosongan'])) {
            return redirect()->route('raks.index')
                ->with('error', 'Rak tidak dapat dihapus karena statusnya masih ' . $rak->status . '!');
        }

        DB::beginTransaction();
        try {
            foreach ($rak->fotos as $foto) {
                $foto->deleteFile();
                $foto->delete();
            }

            if ($rak->foto && Storage::disk('public')->exists($rak->foto)) {
                Storage::disk('public')->delete($rak->foto);
            }

            $rak->delete();

            DB::commit();

            return redirect()->route('raks.index')
                ->with('success', 'Rak berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting rak: ' . $e->getMessage());

            return redirect()->route('raks.index')
                ->with('error', 'Gagal menghapus rak: ' . $e->getMessage());
        }
    }

    private function handleMultiplePhotos($files, $rak)
    {
        $existingCount = $rak->fotos()->count();
        $isPrimarySet = $rak->fotos()->where('is_primary', true)->exists();

        $maxAllowed = self::MAX_PHOTOS - $existingCount;
        $filesToProcess = array_slice($files, 0, $maxAllowed);

        foreach ($filesToProcess as $index => $file) {
            $filename = time() . '_' . $index . '_' . Str::slug($rak->nama_rak) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('raks', $filename, 'public');

            FotoRak::create([
                'rak_id' => $rak->id,
                'path' => $path,
                'is_primary' => !$isPrimarySet && $index === 0,
                'urutan' => $existingCount + $index
            ]);
        }
    }

    private function deletePhotos($photoIds, $rakId)
    {
        $fotos = FotoRak::where('rak_id', $rakId)->whereIn('id', $photoIds)->get();

        foreach ($fotos as $foto) {
            $foto->deleteFile();
            $foto->delete();
        }

        $remainingFotos = FotoRak::where('rak_id', $rakId)->orderBy('urutan')->get();
        foreach ($remainingFotos as $index => $foto) {
            $foto->update(['urutan' => $index]);
        }
    }

    private function updatePrimaryPhoto($photoId, $rakId)
    {
        FotoRak::where('rak_id', $rakId)->update(['is_primary' => false]);
        FotoRak::where('id', $photoId)->where('rak_id', $rakId)->update(['is_primary' => true]);
    }
}