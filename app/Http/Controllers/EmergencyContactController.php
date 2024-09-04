<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function get_emergency_contacts()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        return response()->json($user->emergency_contacts, 200);
    }
    public function add_emergency_contact(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'name'=>'required|max:255',
            'relationship'=>'required|max:255',
            'professional_situation'=>'required|max:255',
            'phone_number_1'=>'required|max:20',
            'phone_number_2'=>'required|max:20',
            'phone_number_3'=>'required|max:20',
        ]);
        $user->emergencyContacts()->create([
            'name' => $validatedData['name'],
            'relationship' => $validatedData['relationship'],
            'professional_situation' => $validatedData['professional_situation'],
            'phone_number_1' => $validatedData['phone_number_1'],
            'phone_number_2' => $validatedData['phone_number_2'],
            'phone_number_3' => $validatedData['phone_number_3'],
        ]);
        return response()->json(['message'=> 'Success'], 200);
    }
    public function delete_emergency_contact(Request $request, $id)
    {
        $user = Auth::user();
        $contact = $user->emergency_contacts->find($id);
        $contact->delete();
        return response()->json(['message'=> 'Success'], 200);
    }
    public function update_emergency_contact(Request $request, $id)
    {
        $user = Auth::user();
        $contact = $user->emergency_contacts->find($id);
        $validatedData = $request->validate([
            'name'=>'required|max:255',
            'relationship'=>'required|max:255',
            'professional_situation'=>'required|max:255',
            'phone_number_1'=>'required|max:20',
            'phone_number_2'=>'required|max:20',
            'phone_number_3'=>'required|max:20',
        ]);
        $contact->name = $validatedData['name'];
        $contact->relationship = $validatedData['relationship'];
        $contact->professional_situation = $validatedData['professional_situation'];
        $contact->phone_number_1 = $validatedData['phone_number_1'];
        $contact->phone_number_2 = $validatedData['phone_number_2'];
        $contact->phone_number_3 = $validatedData['phone_number_3'];
    }
}
