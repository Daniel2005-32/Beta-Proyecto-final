<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Helpers\CensorHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Verificar si el usuario está baneado
     */
    private function checkBanned()
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return response()->json(['error' => 'No puedes enviar mensajes mientras estás baneado'], 403);
        }
        return null;
    }

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
        $check = $this->checkBanned();
        if ($check) return $check;

        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $limitDate = now()->subHours(1);
        Message::where('created_at', '<', $limitDate)->delete();

        return response()->json([
            'success' => true,
            'id' => $message->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'message' => CensorHelper::censor($message->message),
            'time' => $message->created_at->diffForHumans()
        ]);
    }
}
