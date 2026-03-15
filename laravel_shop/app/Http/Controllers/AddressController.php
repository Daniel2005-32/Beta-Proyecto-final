<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return response()->json(['addresses' => $addresses]);
    }

    public function store(Request $request)
    {
        // Validar los datos (sin neighborhood)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:10',
        ]);

        // Preparar los datos
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        // Verificar si es la primera dirección
        $existingAddresses = Address::where('user_id', Auth::id())->count();
        $data['is_default'] = $request->has('is_default') || $existingAddresses == 0;

        // Si es predeterminada, quitar el default de las otras
        if ($data['is_default']) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        // Crear la dirección
        $address = Address::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dirección guardada correctamente',
            'address' => $address
        ], 201);
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id != Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:10',
        ]);

        $data = $request->all();
        
        if ($request->has('is_default') && !$address->is_default) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada',
            'address' => $address
        ]);
    }

    public function destroy(Address $address)
    {
        if ($address->user_id != Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $firstAddress = Address::where('user_id', Auth::id())->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada'
        ]);
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id != Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección predeterminada actualizada'
        ]);
    }
}
