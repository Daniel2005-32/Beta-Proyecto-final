<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Raffle;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RaffleController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $raffles = Raffle::with('winner')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.raffles.index', compact('raffles'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $products = Product::where('stock', '>', 0)->get();
        return view('admin.raffles.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'ticket_price' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_entries' => 'nullable|integer|min:1',
        ]);

        $extraData = [
            'product_id' => $request->product_id,
            'product_name' => Product::find($request->product_id)->name,
            'ticket_price' => $request->ticket_price,
            'max_entries' => $request->max_entries,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        $description = $request->description . "\n\n[DATOS_SORTEO]\n" . json_encode($extraData);

        $now = Carbon::now();
        $startDate = Carbon::parse($request->start_date);
        
        $status = $now->lt($startDate) ? 'pending' : 'pending'; // Siempre pending al crear

        Raffle::create([
            'title' => $request->title,
            'description' => $description,
            'draw_date' => $request->end_date,
            'status' => $status,
        ]);

        return redirect()->route('admin.raffles.index')
            ->with('success', 'Sorteo creado correctamente');
    }

    public function edit(Raffle $raffle)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $extraData = $raffle->getExtraData();
        $products = Product::where('stock', '>', 0)->get();
        
        return view('admin.raffles.edit', compact('raffle', 'products', 'extraData'));
    }

    public function update(Request $request, Raffle $raffle)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'ticket_price' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_entries' => 'nullable|integer|min:1',
        ]);

        $extraData = [
            'product_id' => $request->product_id,
            'product_name' => Product::find($request->product_id)->name,
            'ticket_price' => $request->ticket_price,
            'max_entries' => $request->max_entries,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        $newDescription = $request->description . "\n\n[DATOS_SORTEO]\n" . json_encode($extraData);

        $raffle->update([
            'title' => $request->title,
            'description' => $newDescription,
            'draw_date' => $request->end_date,
        ]);

        return redirect()->route('admin.raffles.index')
            ->with('success', 'Sorteo actualizado correctamente');
    }

    public function destroy(Raffle $raffle)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $raffle->delete();
        return redirect()->route('admin.raffles.index')
            ->with('success', 'Sorteo eliminado');
    }

    public function activate(Raffle $raffle)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $raffle->update(['status' => 'pending']);
        
        return redirect()->route('admin.raffles.index')
            ->with('success', 'Sorteo activado manualmente');
    }

    public function drawWinner(Raffle $raffle)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        if ($raffle->status === 'completed') {
            return redirect()->route('admin.raffles.index')
                ->with('error', 'Este sorteo ya tiene un ganador');
        }

        $winnerId = $raffle->drawWinner();
        
        if ($winnerId) {
            return redirect()->route('admin.raffles.index')
                ->with('success', '¡Ganador seleccionado correctamente!');
        } else {
            return redirect()->route('admin.raffles.index')
                ->with('error', 'No hay participantes en este sorteo');
        }
    }
}
