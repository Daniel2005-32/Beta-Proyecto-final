<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Admin: List all tickets
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || (!$user->is_admin && !$user->is_super_admin)) {
                \Log::warning("Unauthorized support access attempt by user: " . ($user ? $user->id : 'Guest'));
                return response()->json(['error' => 'No tienes permisos de administrador.'], 403);
            }

            $tickets = SupportTicket::with('user:id,name,email')
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info("Admin Support: Retrieved " . $tickets->count() . " tickets. Total in DB: " . SupportTicket::count());

            return response()->json([
                'tickets' => $tickets
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in SupportController@index: " . $e->getMessage());
            return response()->json(['error' => 'Error al listar tickets: ' . $e->getMessage()], 500);
        }
    }

    /**
     * User: List my tickets
     */
    public function userTickets(Request $request)
    {
        return response()->json([
            'tickets' => SupportTicket::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    /**
     * User: Create a ticket
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                \Log::error("Support submission failed: User not authenticated.");
                return response()->json(['error' => 'Usuario no autenticado.'], 401);
            }

            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            \Log::info("Attempting to create ticket for user: " . $user->id);

            $ticket = new SupportTicket();
            $ticket->user_id = $user->id;
            $ticket->subject = $request->subject;
            $ticket->message = $request->message;
            $ticket->status = 'open';
            $ticket->save();

            \Log::info("Ticket created successfully: " . $ticket->id);

            return response()->json([
                'message' => 'Ticket de soporte creado exitosamente.',
                'ticket' => $ticket
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos inválidos.', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error("FATAL ERROR in SupportController@store: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin: Reply and update status
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_reply' => 'nullable|string',
            'status' => 'required|in:open,pending,closed'
        ]);

        $ticket->update($request->only('admin_reply', 'status'));

        return response()->json([
            'message' => 'Ticket actualizado correctamente.',
            'ticket' => $ticket->load('user:id,name,email')
        ]);
    }

    /**
     * Delete a ticket
     */
    public function destroy(Request $request, SupportTicket $ticket)
    {
        try {
            $user = $request->user();
            
            // Perímetro de seguridad: Solo el dueño o un admin pueden borrar
            if ($user->id !== $ticket->user_id && !$user->is_admin && !$user->is_super_admin) {
                return response()->json(['error' => 'No tienes permiso para eliminar este ticket.'], 403);
            }

            $ticket->delete();

            return response()->json([
                'message' => 'Ticket eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in SupportController@destroy: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el ticket.'], 500);
        }
    }
}
