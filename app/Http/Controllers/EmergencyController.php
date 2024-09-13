<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyController extends Controller
{
    public function index()
    {
        $emergencies = Emergency::all();
        return response()->json($emergencies);
    }

    // Afficher une seule urgence
    public function show($id)
    {
        $emergency = Emergency::findOrFail($id);
        return response()->json($emergency);
    }

    // Créer une nouvelle urgence
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number_1' => 'required|string|max:15',
            'phone_number_2' => 'nullable|string|max:15',
            'phone_number_3' => 'nullable|string|max:15',
        ]);

        $emergency = Emergency::create($validatedData);
        return response()->json($emergency, 200);
    }

    // Mettre à jour une urgence existante
    public function update(Request $request, $id)
    {
        $emergency = Emergency::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number_1' => 'required|string|max:15',
            'phone_number_2' => 'nullable|string|max:15',
            'phone_number_3' => 'nullable|string|max:15',
        ]);

        $emergency->update($validatedData);
        return response()->json($emergency, 200);
    }

    // Supprimer une urgence
    public function destroy($id)
    {
        $emergency = Emergency::findOrFail($id);
        $emergency->delete();

        return response()->json(null, 200);
    }
    /**
     * @OA\Post(
     *     path="/register_member",
     *     summary="Register a new member",
     *     description="Enregistre un nouvel utilisateur avec les informations fournies.",
     *     tags={"Emergency"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "matricule", "phone_number", "position", "email", "username", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="matricule", type="string", example="M123456"),
     *             @OA\Property(property="phone_number", type="string", example="0612345678"),
     *             @OA\Property(property="position", type="string", example="Developer"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="photo", type="string", format="binary", description="Image file for the user's photo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur ajouté avec succès",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Utilisateur ajouté avec succès"
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
    public function register_member(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'max:255', 'unique:users,matricule'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'position' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'photo' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);
        $photo = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $photo = asset('storage/' . $path);
        }
        User::create([
            'role_id' => 4,
            'name' => $validatedData['name'],
            'matricule' => $validatedData['matricule'],
            'phone_number' => $validatedData['phone_number'],
            'position' => $validatedData['position'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'photo' => $photo
        ]);
        return response()->json('Utilisateur ajouté avec succès', 200);
    }
}
