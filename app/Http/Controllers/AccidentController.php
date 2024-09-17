<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AccidentController extends Controller
{
    public function launch_alert(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];

        $client=new Client(['defaults' => [
            'verify' => false
        ]]
        );
        $response = $client->get("https://nominatim.openstreetmap.org/reverse", [
            'query' => [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 18,
                'addressdetails' => 1,
            ],
        ]);
        $data='';
        if (isset($data['address'])) {
            // Extraire la ville et le quartier
            $city = $data['address']['city'] ?? $data['address']['town'] ?? 'Ville non trouvée';
            $neighborhood = $data['address']['suburb'] ?? $data['address']['neighbourhood'] ?? 'Quartier non trouvé';

            return response()->json([
                'city' => $city,
                'neighborhood' => $neighborhood,
                'full_address' => $data['display_name'],
            ]);
        }
    }
}
