<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\RaffleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaffleController extends Controller
{
    /**
     * Listar sorteos activos/pendientes
     */
    public function index()
    {
        // Traer sorteos cuyo estado no sea completado
        $raffles = Raffle::where('status', 'pending')
            ->orderBy('draw_date', 'asc')
            ->get()
            ->map(function ($raffle) {
                return $this->formatRaffle($raffle, true);
            });

        return response()->json([
            'status' => 'success',
            'raffles' => $raffles
        ]);
    }

    /**
     * Mostrar detalles de un sorteo por ID
     */
    public function show($id)
    {
        $raffle = Raffle::find($id);

        if (!$raffle) {
            return response()->json(['status' => 'error', 'message' => 'Sorteo no encontrado'], 404);
        }

        $formatted = $this->formatRaffle($raffle, true);

        return response()->json([
            'status' => 'success',
            'raffle' => $formatted
        ]);
    }

    /**
     * Participar en un sorteo (Comprar tickets)
     */
    public function enter(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $raffle = Raffle::find($id);

        if (!$raffle) {
            return response()->json(['status' => 'error', 'message' => 'Sorteo no encontrado'], 404);
        }

        if ($raffle->status === 'completed') {
            return response()->json(['status' => 'error', 'message' => 'Este sorteo ya ha finalizado'], 400);
        }

        $user = Auth::user();

        // Calcular costo
        $pricePerTicket = $raffle->getTicketPrice();
        $quantity = $request->quantity;
        $totalCost = $pricePerTicket * $quantity;

        // Validar límite total si existe
        $maxEntries = $raffle->getMaxEntries();
        $currentEntries = $raffle->entries()->sum('quantity');

        if ($maxEntries && ($currentEntries + $quantity) > $maxEntries) {
            return response()->json([
                'status' => 'error', 
                'message' => 'No quedan suficientes entradas disponibles. Quedan: ' . ($maxEntries - $currentEntries)
            ], 400);
        }

        // Crear Entrada
        $entry = RaffleEntry::create([
            'raffle_id' => $raffle->id,
            'user_id' => $user->id,
            'quantity' => $quantity,
            'amount_spent' => $totalCost
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Te has registrado en el sorteo exitosamente',
            'entry' => $entry
        ]);
    }

    /**
     * Helper para formatear datos JSON que incluyen métodos del modelo
     */
    private function formatRaffle($raffle, $includeUserStats = false)
    {
        $product = $raffle->getProduct();
        
        $data = [
            'id' => $raffle->id,
            'title' => $raffle->title,
            'description' => $raffle->getCleanDescription(),
            'draw_date' => $raffle->draw_date,
            'status' => $raffle->status,
            'ticket_price' => $raffle->getTicketPrice(),
            'max_entries' => $raffle->getMaxEntries(),
            'total_entries' => $raffle->entries()->sum('quantity'),
            'time_left' => $raffle->timeLeft(),
            'is_active' => $raffle->isActive(),
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'price' => $product->price
            ] : null
        ];

        if ($raffle->status === 'completed' && $raffle->winner_id) {
            $data['winner'] = $raffle->winner()->select('id', 'name')->first();
        }

        if ($includeUserStats && Auth::check()) {
            $userId = Auth::id();
            $data['user_entries'] = $raffle->getUserEntries($userId);
            $data['user_chance'] = $raffle->getUserChance($userId);
        }

        return $data;
    }
}
