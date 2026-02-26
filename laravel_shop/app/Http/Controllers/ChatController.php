<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Helpers\CensorHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name,
                    'message' => CensorHelper::censor($msg->message), // Censurar aquí
                    'original_message' => $msg->message, // Solo para debugging (opcional)
                    'time' => $msg->created_at->diffForHumans()
                ];
            })
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    public function refresh()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Verificar si contiene malas palabras (opcional - puedes rechazar o aceptar)
        $containsBadWords = CensorHelper::containsBadWords($request->message);
        
        // Guardar el mensaje original (sin censurar en BD)
        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message, // Guardamos el original
        ]);

        // Para la respuesta, devolvemos el mensaje censurado
        return response()->json([
            'success' => true,
            'id' => $message->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'message' => CensorHelper::censor($message->message), // Censurar para mostrar
            'time' => $message->created_at->diffForHumans(),
            'was_censored' => $containsBadWords
        ]);
    }
}
