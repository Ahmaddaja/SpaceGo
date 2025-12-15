<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\Gudang;
use App\Models\Transaction;
use App\Models\FotoRak;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RakController extends Controller
{
    // Konstanta untuk maksimal foto
    const MAX_PHOTOS = 4;

    private function autoUpdateRakStatuses($userId)
    {
        // Ambil semua tagihan settlement milik user
        $tagihans = Tagihan::where('user_id', $userId)
            ->where('status', 'settlement')
            ->whereNotNull('sewa_berakhir')
            ->get();

        foreach ($tagihans as $tagihan) {
            $tagihan->updateStatusRak();
        }
    }

    public function rakDibeli()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(403, 'User tidak ditemukan.');
            }

            // Auto-update semua status rak
            $this->autoUpdateRakStatuses($user->id);

            // Ambil transactions dengan relasi tagihan
            $transactions = Transaction::where('user_id', $user->id)
                ->whereIn('transaction_status', ['capture', 'settlement'])
                ->with(['rak.gudang', 'rak.fotos', 'tagihan'])
                ->orderBy('created_at', 'desc')
                ->get();

            $rakIds = $transactions->pluck('rak_id')->unique();

            $raks = Rak::whereIn('id', $rakIds)
                ->with(['gudang', 'fotos'])
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            $raks->getCollection()->transform(function ($rak) use ($transactions) {
                $transaction = $transactions->where('rak_id', $rak->id)->first();

                if ($transaction && $transaction->tagihan) {
                    $tagihan = $transaction->tagihan;

                    $rak->transaction_date = $transaction->created_at;
                    $rak->order_id = $transaction->order_id;
                    $rak->payment_type = $transaction->payment_type;
                    $rak->transaction_id = $transaction->id;

                    // Status rak dari tagihan
                    $rak->status_rak_sewa = $tagihan->status_rak;
                    $rak->status_rak_info = $tagihan->getStatusRakInfo();

                    // Info pengosongan
                    $rak->is_pengosongan = $tagihan->is_pengosongan ?? false;
                    $rak->pengosongan_dimulai = $tagihan->pengosongan_dimulai ?? null;
                    $rak->pengosongan_berakhir = $tagihan->pengosongan_berakhir ?? null;
                    $rak->is_dikosongkan = $tagihan->is_dikosongkan ?? false;
                    $rak->dikosongkan_at = $tagihan->dikosongkan_at ?? null;
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

        // Update status sebelum menampilkan detail
        $this->autoUpdateRakStatuses($user->id);

        $transaction = Transaction::where('user_id', $user->id)
            ->where('rak_id', $id)
            ->whereIn('transaction_status', ['capture', 'settlement'])
            ->with(['rak.gudang', 'rak.fotos', 'tagihan'])
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

        // Status dari tagihan
        if ($transaction->tagihan) {
            $tagihan = $transaction->tagihan;
            $rak->status_sewa = $tagihan->status;
            $rak->status_rak_sewa = $tagihan->status_rak;
            $rak->status_rak_info = $tagihan->getStatusRakInfo();

            // Hitung sisa hari
            if ($tagihan->sewa_berakhir) {
                $now = now();
                $end = \Carbon\Carbon::parse($tagihan->sewa_berakhir);
                $rak->sisa_hari = $now->diffInDays($end, false);
            }
        }

        return view('customer.list-rak.show', compact('rak'));
    }

    // Rest of your existing methods...
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
            ->with(['rak', 'tagihan'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('customer.list-rak.rak')
                ->with('error', 'Tidak ada riwayat transaksi untuk rak ini.');
        }

        $rak = Rak::findOrFail($id);

        return view('customer.list-rak.show', compact('transactions', 'rak'));
    }


    public function index()
    {
        $raks = Rak::with(['fotos', 'gudang'])->latest()->paginate(10);
        return view('admin.raks.index', compact('raks'));
    }
    public function create()
    {
        // ✅ BARU: Cleanup temp photos dari session sebelumnya
        $oldTempPhotos = session()->get('temp_rak_photos', []);

        if (!empty($oldTempPhotos)) {
            foreach ($oldTempPhotos as $photo) {
                if (Storage::disk('public')->exists($photo['path'])) {
                    Storage::disk('public')->delete($photo['path']);
                }
            }

            Log::info('Cleaned up old temp photos on create page load: ' . count($oldTempPhotos) . ' files');
        }

        // ✅ BARU: Reset session untuk fresh start
        session()->forget('temp_rak_photos');

        // Lanjutkan seperti biasa
        $gudangs = Gudang::where('is_active', true)
            ->orderBy('nama_gudang')
            ->get();

        // ✅ BARU: Kosongkan tempPhotos karena sudah di-reset
        $tempPhotos = [];

        return view('admin.raks.create', compact('gudangs', 'tempPhotos'));
    }
    /**
     * Temporary upload untuk form create (sebelum save)
     */
    public function tempUpload(Request $request)
    {
        try {
            $request->validate([
                'fotos' => 'required|array|max:4',
                'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'fotos.max' => 'Maksimal 4 foto yang dapat diupload.',
                'fotos.*.image' => 'File harus berupa gambar.',
                'fotos.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
                'fotos.*.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            // Ambil existing temp photos dari session
            $tempPhotos = session()->get('temp_rak_photos', []);

            // Cek limit
            if (count($tempPhotos) >= self::MAX_PHOTOS) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 4 foto sudah tercapai!'
                ], 422);
            }

            $maxAllowed = self::MAX_PHOTOS - count($tempPhotos);
            $files = array_slice($request->file('fotos'), 0, $maxAllowed);

            $uploadedPhotos = [];

            foreach ($files as $index => $file) {
                $filename = 'temp_' . time() . '_' . $index . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('raks/temp', $filename, 'public');

                $photoData = [
                    'filename' => $filename,
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'urutan' => count($tempPhotos) + $index
                ];

                $tempPhotos[] = $photoData;
                $uploadedPhotos[] = $photoData;
            }

            // Simpan ke session
            session()->put('temp_rak_photos', $tempPhotos);

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) . ' foto berhasil diupload',
                'fotos' => $uploadedPhotos,
                'total_photos' => count($tempPhotos)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error temp upload: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary photo
     */
    public function tempDelete($filename)
    {
        try {
            $tempPhotos = session()->get('temp_rak_photos', []);

            $photoIndex = array_search($filename, array_column($tempPhotos, 'filename'));

            if ($photoIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto tidak ditemukan'
                ], 404);
            }

            $photo = $tempPhotos[$photoIndex];

            // Hapus file dari storage
            if (Storage::disk('public')->exists($photo['path'])) {
                Storage::disk('public')->delete($photo['path']);
            }

            // Hapus dari array
            unset($tempPhotos[$photoIndex]);
            $tempPhotos = array_values($tempPhotos); // Re-index array

            // Reorder urutan
            foreach ($tempPhotos as $index => &$photo) {
                $photo['urutan'] = $index;
            }

            // Update session
            session()->put('temp_rak_photos', $tempPhotos);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus',
                'total_photos' => count($tempPhotos)
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting temp photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all temporary photos dari session
     */
    public function clearTempPhotos()
    {
        try {
            $tempPhotos = session()->get('temp_rak_photos', []);

            if (empty($tempPhotos)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada foto temporary untuk dibersihkan'
                ]);
            }

            $deletedCount = 0;

            // Hapus semua foto temp dari storage
            foreach ($tempPhotos as $photo) {
                if (Storage::disk('public')->exists($photo['path'])) {
                    Storage::disk('public')->delete($photo['path']);
                    $deletedCount++;
                }
            }

            // Hapus session
            session()->forget('temp_rak_photos');

            Log::info("Temporary photos cleared: {$deletedCount} files deleted");

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} foto temporary berhasil dibersihkan"
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing temp photos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan foto: ' . $e->getMessage()
            ], 500);
        }
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
            'spesifikasi_tambahan' => 'nullable|string',
            'is_active' => 'boolean',
            'durasi_sewa_hari' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $gudang = Gudang::where('nama_gudang', $validated['lokasi_gudang'])->firstOrFail();
            $validated['gudang_id'] = $gudang->id;
            $validated['lokasi_gudang'] = $gudang->nama_gudang;
            $validated['status'] = 'tersedia';

            $rak = Rak::create($validated);

            // Handle temporary photos dari session
            $tempPhotos = session()->get('temp_rak_photos', []);

            if (!empty($tempPhotos)) {
                foreach ($tempPhotos as $index => $tempPhoto) {
                    // Pindahkan dari temp ke permanent folder
                    $oldPath = $tempPhoto['path'];
                    $newFilename = time() . '_' . $index . '_' . Str::slug($rak->nama_rak) . '.' . pathinfo($tempPhoto['filename'], PATHINFO_EXTENSION);
                    $newPath = 'raks/' . $newFilename;

                    // Copy file dari temp ke permanent
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->copy($oldPath, $newPath);
                        Storage::disk('public')->delete($oldPath);

                        // Simpan ke database
                        FotoRak::create([
                            'rak_id' => $rak->id,
                            'path' => $newPath,
                            'urutan' => $index
                        ]);
                    }
                }

                // Clear session
                session()->forget('temp_rak_photos');
            }

            DB::commit();

            return redirect()->route('raks.index')
                ->with('success', 'Rak berhasil ditambahkan!');
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

        // VALIDASI STATUS PENGOSONGAN
        if ($rak->status === 'pengosongan') {
            $pengosonganTransaction = Transaction::where('rak_id', $rak->id)
                ->where('is_pengosongan', true)
                ->whereNotNull('pengosongan_berakhir')
                ->first();

            if ($pengosonganTransaction) {
                $now = \Carbon\Carbon::now();
                $pengosonganEnd = \Carbon\Carbon::parse($pengosonganTransaction->pengosongan_berakhir);

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

            $rak->update($validated);

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

    /**
     * Upload single photo via AJAX
     */
    public function uploadPhoto(Request $request, $id)
    {
        try {
            $rak = Rak::findOrFail($id);

            // Cek jumlah foto existing
            $existingCount = $rak->fotos()->count();

            if ($existingCount >= self::MAX_PHOTOS) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 4 foto sudah tercapai!'
                ], 422);
            }

            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $file = $request->file('foto');
            $filename = time() . '_' . $existingCount . '_' . Str::slug($rak->nama_rak) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('raks', $filename, 'public');

            $foto = FotoRak::create([
                'rak_id' => $rak->id,
                'path' => $path,
                'urutan' => $existingCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload',
                'foto' => [
                    'id' => $foto->id,
                    'url' => asset('storage/' . $foto->path),
                    'path' => $foto->path
                ],
                'total_photos' => $rak->fotos()->count()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple photos secara instant (AJAX)
     */
    public function uploadMultiplePhotos(Request $request, $id)
    {
        try {
            $rak = Rak::findOrFail($id);

            // Cek jumlah foto existing
            $existingCount = $rak->fotos()->count();
            $maxAllowed = self::MAX_PHOTOS - $existingCount;

            if ($maxAllowed <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 4 foto sudah tercapai!'
                ], 422);
            }

            $request->validate([
                'fotos' => 'required|array|max:' . $maxAllowed,
                'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'fotos.max' => 'Maksimal ' . $maxAllowed . ' foto yang dapat diupload.',
                'fotos.*.image' => 'File harus berupa gambar.',
                'fotos.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
                'fotos.*.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            $uploadedPhotos = [];
            $files = $request->file('fotos');

            DB::beginTransaction();

            foreach ($files as $index => $file) {
                $filename = time() . '_' . ($existingCount + $index) . '_' . Str::slug($rak->nama_rak) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('raks', $filename, 'public');

                $foto = FotoRak::create([
                    'rak_id' => $rak->id,
                    'path' => $path,
                    'urutan' => $existingCount + $index
                ]);

                $uploadedPhotos[] = [
                    'id' => $foto->id,
                    'url' => asset('storage/' . $foto->path),
                    'path' => $foto->path
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) . ' foto berhasil diupload',
                'fotos' => $uploadedPhotos,
                'total_photos' => $rak->fotos()->count()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading multiple photos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete photo via AJAX
     */
    public function deletePhoto($id)
    {
        try {
            $foto = FotoRak::findOrFail($id);
            $rakId = $foto->rak_id;

            // Delete file from storage
            if (Storage::disk('public')->exists($foto->path)) {
                Storage::disk('public')->delete($foto->path);
            }

            // Delete from database
            $foto->delete();

            // Reorder remaining photos
            $remainingFotos = FotoRak::where('rak_id', $rakId)->orderBy('urutan')->get();
            foreach ($remainingFotos as $index => $remainingFoto) {
                $remainingFoto->update(['urutan' => $index]);
            }

            $totalPhotos = FotoRak::where('rak_id', $rakId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus',
                'total_photos' => $totalPhotos
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
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
            // Hapus semua foto
            foreach ($rak->fotos as $foto) {
                if (Storage::disk('public')->exists($foto->path)) {
                    Storage::disk('public')->delete($foto->path);
                }
                $foto->delete();
            }

            // Hapus foto lama jika ada
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
}
