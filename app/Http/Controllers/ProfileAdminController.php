<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
}