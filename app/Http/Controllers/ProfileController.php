<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function showProfile() {
        return view('Profile', ['user' => Auth::user()]); //-showProfile from Profile.blade.php
    }

    public function editProfile(Request $request) {
        // Logic to handle profile editing
        $user = User::find(Auth::id()); // Get the currently authenticated user
        $rules = []; // Initialize an empty rules array

        if ($request->filled('name')) {
            $rules['name'] = 'string|max:255';  // Update name
        }
        if ($request->filled('email')) {
            $rules['email'] = 'string|email|max:255|unique:users,email,' . $user->id;  // Update email
        }
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:6|confirmed'; // Update password if provided
        }
        $validated = $request->validate($rules);         //-validation

        // update only filled fields
        if (isset($validated['name'])) {  // Update name
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {  // Update email
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);  // Update password if provided
        }
        $user->save();
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}