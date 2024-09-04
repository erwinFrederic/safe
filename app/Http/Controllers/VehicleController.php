<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function get_vehicles()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $vehicles=$user->vehicles;
        return response()->json($vehicles, 200);
    }
    public function add_vehicle(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $validatedData = $request->validate([
            'brand'=>'required|max:255',
            'model'=>'required|max:255',
            'license'=>'required',
            'color'=>'required|max:255',
        ]);
        $vehicle = new Vehicle();
        $vehicle->brand = $request->brand;
        $vehicle->model = $request->model;
        $vehicle->license = $request->license;
        $vehicle->color = $request->color;
        $user->vehicles()->save($vehicle);
        return response()->json(['message'=> 'Success'], 200);
    }
    public function delete_vehicle(Request $request, $id)
    {
        $user = Auth::user();
        $vehicle = $user->vehicles->find($id);
        $vehicle->delete();
        return response()->json(['message'=> 'Success'], 200);
    }
    public function update_vehicle(Request $request, $id)
    {
        $user = Auth::user();
        $vehicle = $user->vehicles->find($id);
        if($vehicle) {
            $validatedData = $request->validate([
                'brand'=>'required|max:255',
                'model'=>'required|max:255',
                'license'=>'required',
                'color'=>'required|max:255',
            ]);
            $vehicle->brand = $request->brand;
            $vehicle->model = $request->model;
            $vehicle->license = $request->license;
            $vehicle->color = $request->color;
            $vehicle->save();
            return response()->json(['message'=> 'Success'], 200);
        } else {
            return response()->json(['message'=> 'Vehicle not found'], 404);
        }
    }
}
