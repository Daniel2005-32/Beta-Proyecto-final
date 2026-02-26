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
                    'message' => CensorHelper::censor($msg->message),
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

        // Guardar el mensaje
        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // ============================================
        // LIMPIEZA AUTOMÁTICA: Eliminar mensajes de más de 1 hora
        // ============================================
        $limitDate = now()->subHours(1);
        $deleted = Message::where('created_at', '<', $limitDate)->delete();
        
        // Opcional: Registrar en log cuántos se eliminaron
        if ($deleted > 0) {
            \Log::info("🧹 Limpieza automática: {$deleted} mensajes eliminados (más de 1 hora)");
        }

        return response()->json([
            'success' => true,
            'id' => $message->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'message' => CensorHelper::censor($message->message),
            'time' => $message->created_at->diffForHumans()
        ]);
    }

    /**
     * Método adicional para limpieza manual (opcional)
     */
    public function clean()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $limitDate = now()->subHours(1);
        $deleted = Message::where('created_at', '<', $limitDate)->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => "Se eliminaron {$deleted} mensajes antiguos"
        ]);
    }
}
