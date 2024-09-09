<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function update_informations(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'];
        $user->save();
        return response()->json(['message' => 'Success'], 200);
    }
    public function update_email(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->email=$validatedData['email'];
        $user->email_verified_at=now();
        $user->save();
        return response()->json(['message' => 'Success'], 200);
    }
    public function delete_email()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $user->email = null;
        $user->email_verified_at=null;
        $user->save();
        return response()->json(['message' => 'Success'], 200);
    }
    public function verify_number(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'phone_number' => [Rule::unique('users')->ignore($user->id)],
        ]);
        return response()->json(['message' => 'Success'], 200);
    }
    public function update_number(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'phone_number' => [Rule::unique('users')->ignore($user->id)],
        ]);
        $user->phone_number=$validatedData['phone_number'];
        $user->save();
        return response()->json(['message' => 'Success'], 200);
    }
    public function update_password(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'old_password' => 'required',
            'password'=>'required|min:8'
        ]);
        if(Hash::check($request->old_password, $user->password)){
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['message'=> 'Success'], 200);
        }
        return response()->json(['message'=> 'Failed'], 400);
    }
    public function update_photo(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'photo'=>'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        $path = $request->file('photo')->store('photos', 'public');
        $user->photo = asset('storage/'.$path);
        $user->save();
        return response()->json(['message'=> 'Success'], 200);
    }
    public function delete_photo()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $user->photo = null;
        $user->save();
        return response()->json(['message'=> 'Success'], 200);
    }
    public function update_hospital(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'name'=>'required|max:255',
            'address'=>'required|max:255',
            'phone_number'=>'required|max:20',
        ]);
        $user->hospital=json_encode([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number']
        ]);
        $user->save();
        return response()->json(['message'=> 'Success'], 200);
    }
    public function send_verification_email(Request $request)
    {
        $verificationCode = rand(100000, 999999);
        Cache::put('verification_code', $verificationCode);
        Log::info('Le code de confirmation envoyé à l\'email '.$request->email.' est: '. Cache::get('verification_code'));
        return response()->json(['message' => 'Code de vérification envoyé avec succès'],200);
    }
    public function verify_email_code(Request $request)
    {
        $request->validate([
           'verification_code' =>'required|numeric',
        ]);
        if(Cache::get('verification_code') == $request->verification_code){
            Cache::forget('verification_code');
            return response()->json(['message'=> 'Success'], 200);
        }
        return response()->json(['message'=> 'Code de vérification invalide'], 400);
    }


}
