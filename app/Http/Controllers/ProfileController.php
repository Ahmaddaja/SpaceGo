<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile page.
     */
    public function index(Request $request): View
    {
        return view('customer.profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update customer profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'password_lama' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        // Update basic info
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Only update these fields if columns exist in database
        if (\Schema::hasColumn('users', 'phone')) {
            $user->phone = $validated['phone'] ?? null;
        }
        if (\Schema::hasColumn('users', 'perusahaan')) {
            $user->perusahaan = $validated['perusahaan'] ?? null;
        }
        if (\Schema::hasColumn('users', 'alamat')) {
            $user->alamat = $validated['alamat'] ?? null;
        }

        // Update password if provided
        if ($request->filled('password')) {
            if (!\Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = \Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('customer.profile.index')->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Upload profile photo.
     */
    public function uploadFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
            \Storage::disk('public')->delete($user->foto);
        }

        // Store new photo with unique name
        $file = $request->file('foto');
        $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile-photos', $filename, 'public');
        
        $user->foto = $path;
        $user->save();

        return redirect()->route('customer.profile.index')->with('success', 'Foto profile berhasil diperbarui!');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}