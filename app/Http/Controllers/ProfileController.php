<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\AssetRequest;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = auth()->user();
        
        // Fetch active borrowed assets for this user
        $borrowedAssets = AssetRequest::with('asset')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'title' => 'Profile Saya',
            'borrowedAssets' => $borrowedAssets
        ]);
    }

    /**
     * Update the profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Separate validation rules for profile and password to handle them individually if needed, 
        // but since it's one form or two forms on the same page, we can handle it based on input.

        if ($request->has('update_password')) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Password berhasil diubah!');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];

        // Hanya Super Admin yang boleh ubah employee_id
        if (optional($user->role)->slug === 'super_admin') {
            $rules['employee_id'] = ['nullable', 'string', 'max:100'];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'work_location' => $validated['work_location'] ?? null,
        ];

        if (optional($user->role)->slug === 'super_admin' && array_key_exists('employee_id', $validated)) {
            $updateData['employee_id'] = $validated['employee_id'];
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
