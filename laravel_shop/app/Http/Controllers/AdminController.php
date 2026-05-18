<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Raffle;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Verificar si el usuario es administrador
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }
        return null;
    }

    /**
     * Listar todos los usuarios
     */
    public function users()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'users' => $users
        ]);
    }

    /**
     * Crear un Sorteo
     */
    public function createRaffle(Request $request)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'draw_date' => 'required|date',
            'start_date' => 'nullable|date', // Se añade validación
            'ticket_price' => 'nullable|numeric|min:0',
            'max_entries' => 'nullable|integer|min:1',
            'product_id' => 'nullable|exists:products,id',
            'image_url' => 'nullable|string'
        ]);

        $description = $request->description;

        // Construir JSON de datos extra
        $extraData = [
            'ticket_price' => $request->ticket_price ?? 20,
            'max_entries' => $request->max_entries,
            'start_date' => $request->start_date ?? now()->toDateTimeString(), // Usar input si existe
            'end_date' => $request->draw_date,
            'image_url' => $request->image_url
        ];


        if ($request->product_id) {
            $extraData['product_id'] = $request->product_id;
        }

        // Unir JSON a la descripción con la etiqueta del modelo
        $description .= "\n\n[DATOS_SORTEO]\n" . json_encode($extraData);

        $raffle = Raffle::create([
            'title' => $request->title,
            'description' => $description,
            'draw_date' => $request->draw_date,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sorteo creado exitosamente',
            'raffle' => $raffle
        ]);
    }

    /**
     * Realizar Sorteo (Elegir Ganador)
     */
    public function drawRaffle($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $raffle = Raffle::find($id);

        if (!$raffle) {
            return response()->json(['status' => 'error', 'message' => 'Sorteo no encontrado'], 404);
        }

        if ($raffle->status === 'completed') {
            return response()->json(['status' => 'error', 'message' => 'Este sorteo ya ha sido finalizado'], 400);
        }

        // Ejecutar trigger del modelo
        $winnerId = $raffle->drawWinner();

        if (!$winnerId) {
            return response()->json([
                'status' => 'error', 
                'message' => 'No se pudo elegir ganador. Puede que no haya entradas registradas.'
            ], 400);
        }

        $winner = User::find($winnerId);

        return response()->json([
            'status' => 'success',
            'message' => '¡Sorteo finalizado con éxito!',
            'winner' => [
                'id' => $winner->id,
                'name' => $winner->name,
                'email' => $winner->email
            ]
        ]);
    }

    /**
     * Alternar estado de baneo de un usuario
     */
    public function toggleBan($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        if ($user->is_admin) {
            return response()->json(['status' => 'error', 'message' => 'No puedes banear a otro administrador'], 400);
        }

        // Alternar baneo
        $user->is_banned = !$user->is_banned;
        $user->save();

        if ($user->is_banned) {
            broadcast(new \App\Events\UserStatusChanged($user->id, 'banned'));
        }

        return response()->json([
            'status' => 'success',
            'message' => $user->is_banned ? 'Usuario baneado correctamente' : 'Baneo retirado exitosamente',
            'user' => $user
        ]);
    }

    /**
     * Eliminar un usuario permanentemente
     */
    public function deleteUser($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        if ($user->is_admin) {
            return response()->json(['status' => 'error', 'message' => 'No puedes eliminar a otro administrador'], 400);
        }

        $userId = $user->id;
        $user->delete();

        // Notificar al frontend para kickout inmediato
        broadcast(new \App\Events\UserStatusChanged($userId, 'deleted'));

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
    /**
     * Cancelar Sorteo
     */
    public function cancelRaffle($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $raffle = Raffle::find($id);

        if (!$raffle) {
            return response()->json(['status' => 'error', 'message' => 'Sorteo no encontrado'], 404);
        }

        if ($raffle->status !== 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Este sorteo ya ha sido finalizado o cancelado'], 400);
        }

        $raffle->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Sorteo cancelado con éxito',
            'raffle' => $raffle
        ]);
    }

    /**
     * Actualizar Sorteo
     */
    public function updateRaffle(Request $request, $id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $raffle = Raffle::find($id);

        if (!$raffle) {
            return response()->json(['status' => 'error', 'message' => 'Sorteo no encontrado'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'draw_date' => 'required|date',
            'start_date' => 'nullable|date',
            'ticket_price' => 'nullable|numeric|min:0',
            'max_entries' => 'nullable|integer|min:1',
            'product_id' => 'nullable|exists:products,id',
            'image_url' => 'nullable|string'
        ]);

        $description = $request->description;

        $extraData = [
            'ticket_price' => $request->ticket_price ?? 20,
            'max_entries' => $request->max_entries,
            'start_date' => $request->start_date ?? now()->toDateTimeString(),
            'end_date' => $request->draw_date,
            'image_url' => $request->image_url
        ];

        if ($request->product_id) {
            $extraData['product_id'] = $request->product_id;
        }

        $description .= "\n\n[DATOS_SORTEO]\n" . json_encode($extraData);

        $raffle->update([
            'title' => $request->title,
            'description' => $description,
            'draw_date' => $request->draw_date
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sorteo actualizado exitosamente',
            'raffle' => $raffle
        ]);
    }

    /**
     * Listar todos los baneos activos
     */
    public function listBans()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $bans = \App\Models\Ban::with('user')
            ->where(function($query) {
                $query->where('banned_until', '>', now())
                      ->orWhere('is_permanent', true);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'bans' => $bans
        ]);
    }

    /**
     * Extender baneo
     */
    public function extendBan(Request $request, $id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'hours' => 'required|integer|min:1'
        ]);

        $ban = \App\Models\Ban::findOrFail($id);
        $ban->banned_until = \Carbon\Carbon::parse($ban->banned_until ?? now())->addHours($request->hours);
        $ban->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tiempo de baneo extendido correctamente',
            'ban' => $ban
        ]);
    }

    /**
     * Reducir o eliminar baneo
     */
    public function reduceBan(Request $request, $id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $request->validate([
            'hours' => 'required|integer|min:1'
        ]);

        $ban = \App\Models\Ban::findOrFail($id);
        
        if (!$ban->banned_until) {
             return response()->json(['status' => 'error', 'message' => 'No se puede reducir un baneo sin fecha límite'], 400);
        }

        $ban->banned_until = \Carbon\Carbon::parse($ban->banned_until)->subHours($request->hours);
        if ($ban->banned_until <= now()) {
            $ban->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Baneo levantado permanentemente'
            ]);
        }
        $ban->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tiempo de baneo reducido correctamente',
            'ban' => $ban
        ]);
    }
}

