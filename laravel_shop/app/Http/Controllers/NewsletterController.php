<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.unique' => 'Este correo ya está suscrito a nuestro newsletter.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
            'email.required' => 'El correo electrónico es obligatorio.'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        Newsletter::create([
            'email' => $request->email
        ]);

        return response()->json(['message' => '¡Gracias por suscribirte a nuestro newsletter! 🎉']);
    }
}
