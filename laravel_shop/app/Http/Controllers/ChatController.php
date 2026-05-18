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

    public function index(Request $request)
    {
        $messages = Message::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->map(function($msg) use ($request) {
                $canSeeCensored = Auth::check() && Auth::user()->is_admin && $request->query('admin') === 'true';
                return [
                    'id' => $msg->id,
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name,
                    'is_super_admin' => $msg->user->is_super_admin ?? false,
                    'message' => $canSeeCensored ? $msg->message : CensorHelper::censor($msg->message),
                    'time' => $msg->created_at->diffForHumans()
                ];
            })
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    public function refresh(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        $check = $this->checkBanned();
        if ($check) return $check;

        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $wordsFound = CensorHelper::getBadWordsFound($request->message);
        if (count($wordsFound) > 0) {
            $cacheKey = "user_censored_count_" . Auth::id();
            $count = \Illuminate\Support\Facades\Cache::get($cacheKey, 0) + count($wordsFound);
            \Illuminate\Support\Facades\Cache::put($cacheKey, $count, now()->addHours(1));

            if ($count >= 5) {
                \App\Models\Ban::create([
                    'user_id' => Auth::id(),
                    'reason' => 'Exceso de palabras censuradas en chat (automático)',
                    'banned_until' => now()->addHour(),
                    'is_permanent' => false
                ]);
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                return response()->json(['error' => 'Has sido bloqueado del chat por 1 hora por usar palabras censuradas.'], 403);
            }
        }

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
            'is_super_admin' => Auth::user()->is_super_admin ?? false,
            'message' => CensorHelper::censor($message->message),
            'time' => $message->created_at->diffForHumans()
        ]);
    }

    public function clear()
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        \App\Models\Message::query()->delete();

        return response()->json(['success' => true, 'message' => 'Chat vaciado correctamente']);
    }
}
