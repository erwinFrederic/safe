<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function update_informations(Request $request)
    {
        $user = Auth::user();

        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'blood_type' => 'required|string|max:3',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $user->name = $validatedData['name'] ?? $user->name;
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'] ?? $user->email;
        $user->phone_number = $validatedData['phone_number'];
        $user->blood_type = $validatedData['blood_type'] ?? $user->blood_type;
        $user->hospital = $validatedData['hospital'] ?? $user->hospital;
    }
}
