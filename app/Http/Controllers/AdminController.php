<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/create_emergency",
     *     summary="Create a new emergency",
     *     description="Crée une nouvelle entrée pour une urgence et un utilisateur associé.",
     *     tags={"Emergency"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "emplacement", "email", "phone_number", "username", "password"},
     *             @OA\Property(property="nom", type="string", example="Urgence Médicale"),
     *             @OA\Property(property="emplacement", type="string", example="123 Rue Exemple, Paris"),
     *             @OA\Property(property="email", type="string", format="email", example="urgence@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="0612345678"),
     *             @OA\Property(property="username", type="string", example="emergencyuser"),
     *             @OA\Property(property="password", type="string", format="password", example="securepassword"),
     *             @OA\Property(property="logo", type="string", format="binary", description="Image file for the emergency's logo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Success"
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="field_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The field_name field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function create_emergency(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'emplacement' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:emergencies,email'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:emergencies,phone_number_1'],
            'username' => ['required', 'string', 'max:255', 'unique:emergencies,username'],
            'password' => ['required', 'string', 'min:8'],
            'logo' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);
        $logo = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logo', 'public');
            $logo = asset('storage/' . $path);
        }
        $emergency = Emergency::create([
            'name' => $request['nom'],
            'address' => $request['emplacement'],
            'email' => $request['email'],
            'phone_number_1' => $request['phone_number'],
            'logo' => $logo
        ]);
        User::create([
            'username' => $request['username'],
            'password' => bcrypt($request['password']),
            'role_id' => 3,
            'emergency_id' => $emergency->id
        ]);
        return response()->json('Success', 200);
    }
}
