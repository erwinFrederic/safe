<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function actualise()
    {
        $user = Auth::user();
        return response()->json(['user'=>$user], 200);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'sex' => 'required|string',
            'password' => 'required|string|min:8',
            'blood_type' => 'required|string',
            'birth_date' => 'required|string'
        ]);

        $user = User::create([
            'role_id'=>1,
            'name' => $request->name,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'sex' => $request->sex,
            'blood_type' => $request->blood_type,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Registration successful','user'=>$user, 'access_token' => $token], 200);
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'access_token' => $token,'user'=>$user], 200);
        }

        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }
    public function username_verify(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if($user){
            return response()->json(['message'=> 'Username is already taken'], 400);
        }
        return response()->json(['message'=> 'Username is available'], 200);
    }
    public function phone_number_verify(Request $request)
    {
        $user = User::where('phone_number', $request->phone_number)->first();
        if($user){
            return response()->json(['message'=> 'phone_number is already taken'], 400);
        }
        return response()->json(['message'=> 'phone_number is available'], 200);
    }

    public function email_verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user){
            return response()->json(['message'=> 'Email is already taken'], 400);
        }
        return response()->json(['message'=> 'Email is available'], 200);
    }

    public function send_verification_sms(Request $request)
    {
        $verificationCode = rand(100000, 999999);
        Cache::put('verification_code', $verificationCode);
        Log::info('Le code de confirmation envoyé au numéro '.$request->phone_number.' est: '. Cache::get('verification_code'));
        return response()->json(['message' => 'Code de vérification envoyé avec succès'],200);
    }

    public function verify_code(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|numeric',
        ]);

        $sentCode = Cache::get('verification_code');
        $userCode = $request->input('verification_code');
        Log::info(Cache::get('verification_code').' et '.$userCode);
        if ($sentCode == $userCode) {
            Cache::forget('verification_code');
            return response()->json(['message' => 'Numéro vérifié avec succès'], 200);
        } else {
            return response()->json(['error' => 'Code de vérification incorrect'], 400);
        }
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    // Oubli de mot de passe
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent'], 200);
        }

        return response()->json(['message' => 'Unable to send password reset link'], 400);
    }
}
