<?php

namespace App\Http\Controllers;

use App\Models\Emergency;
use Illuminate\Http\Request;

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
        return response()->json($emergency,200);
    }

    // Supprimer une urgence
    public function destroy($id)
    {
        $emergency = Emergency::findOrFail($id);
        $emergency->delete();

        return response()->json(null, 200);
    }
}
