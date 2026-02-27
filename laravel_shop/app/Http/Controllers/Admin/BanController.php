<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ban;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BanController extends Controller
{
    public function index()
    {
        $bans = Ban::with(['user', 'banner'])
            ->latest()
            ->paginate(20);
            
        return view('admin.bans.index', compact('bans'));
    }

    public function create()
    {
        $users = User::whereDoesntHave('bans', function($q) {
            $q->where('is_permanent', true)
              ->orWhere('banned_until', '>', Carbon::now());
        })->get();
        
        return view('admin.bans.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
            'duration' => 'required',
        ]);

        $user = User::find($request->user_id);
        
        $duration = $request->duration;
        
        // Si no es permanente, calcular horas correctamente
        if ($duration !== 'permanent') {
            $hours = (int) $duration;
            if ($request->has('duration_unit') && $request->duration_unit === 'days') {
                $hours = $hours * 24;
            }
        } else {
            $hours = 'permanent';
        }

        $user->ban($request->reason, $hours);

        return redirect()->route('admin.bans.index')
            ->with('success', 'Usuario baneado correctamente');
    }

    public function edit(Ban $ban)
    {
        return view('admin.bans.edit', compact('ban'));
    }

    public function update(Request $request, Ban $ban)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $ban->update([
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.bans.index')
            ->with('success', 'Baneo actualizado');
    }

    public function destroy(Ban $ban)
    {
        $ban->delete();
        
        return redirect()->route('admin.bans.index')
            ->with('success', 'Baneo eliminado');
    }

    public function unban(User $user)
    {
        $user->unban();
        
        return redirect()->route('admin.bans.index')
            ->with('success', 'Usuario desbaneado correctamente');
    }
}
