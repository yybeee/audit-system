<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user()
        ]);
    }
    
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);
        
        Auth::user()->update($validated);
        
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
    
    public function password()
    {
        return view('profile.password', [
            'user' => Auth::user()
        ]);
    }
    
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        
        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return redirect()->route('profile.password')->with('success', 'Password updated successfully!');
    }
}