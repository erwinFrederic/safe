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
    /**
     * @OA\Get(
     *     path="/actualise",
     *     summary="Actualiser les informations de l'utilisateur",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations de l'utilisateur actualisées",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
    public function actualise()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $user->load('vehicles','emergencyContacts');
        return response()->json(['user'=>$user], 200);
    }
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Enregistrer un nouvel utilisateur",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="phone_number", type="string", example="+123456789"),
     *             @OA\Property(property="sex", type="string", example="male"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="blood_type", type="string", example="O+"),
     *             @OA\Property(property="birth_date", type="string", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inscription réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="access_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation échouée"
     *     )
     * )
     */
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
        $user->load('vehicles','emergencyContacts');

        return response()->json(['message' => 'Registration successful','user'=>$user, 'access_token' => $token], 200);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Connecter un utilisateur",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="access_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
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
            $user->load('vehicles','emergencyContacts');
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'access_token' => $token,'user'=>$user], 200);
        }

        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/username_verify",
     *     summary="Vérifier la disponibilité du nom d'utilisateur",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", example="johndoe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nom d'utilisateur disponible"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Nom d'utilisateur déjà pris"
     *     )
     * )
     */
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
    public function logout()
    {
        /** @var \App\Models\User $user **/
        $user=Auth::user();
        $user->tokens()->delete();
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
