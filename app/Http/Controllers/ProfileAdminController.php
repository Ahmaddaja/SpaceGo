<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class ProfileAdminController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        return view('admin.profile.index');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'name.max' => 'Nama lengkap maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'phone.max' => 'Nomor telepon maksimal 15 karakter',
            'address.max' => 'Alamat maksimal 500 karakter',
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Check if new password is different from current
        if (Hash::check($validated['new_password'], $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru harus berbeda dengan password saat ini']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Upload user's profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'photo.required' => 'Foto wajib dipilih',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus JPG, JPEG, atau PNG',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            $user = Auth::user();
            
            // Cek kolom foto di database - gunakan 'foto' karena biasanya di Indonesia menggunakan 'foto'
            $photoColumn = 'foto';

            // Jika kolom foto tidak ada, coba kolom lain
            if (!Schema::hasColumn('users', $photoColumn)) {
                // Coba kolom alternatif
                $possibleColumns = ['photo', 'profile_photo', 'profile_photo_path', 'avatar', 'gambar'];
                foreach ($possibleColumns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $photoColumn = $column;
                        break;
                    }
                }
                
                // Jika tidak ada kolom foto sama sekali, buat kolom baru
                if (!Schema::hasColumn('users', $photoColumn)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kolom untuk foto profile tidak ditemukan. Silakan tambahkan kolom "foto" di tabel users.'
                    ], 500);
                }
            }

            // Delete old photo if exists
            if ($user->$photoColumn && Storage::disk('public')->exists($user->$photoColumn)) {
                Storage::disk('public')->delete($user->$photoColumn);
            }

            // Store new photo in profiles-photos directory
            $file = $request->file('photo');
            $filename = 'admin_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profiles-photos', $filename, 'public');
            
            // Update user photo
            $user->update([
                $photoColumn => $path
            ]);

            return response()->json([
                'success' => true,
                'photo_url' => Storage::url($path) . '?t=' . time(), // Tambahkan timestamp untuk cache busting
                'message' => 'Foto profile berhasil diupload!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload photo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }
}