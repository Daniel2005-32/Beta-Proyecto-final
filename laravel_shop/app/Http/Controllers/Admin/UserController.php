<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $query = User::query();

        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        if (request()->has('sort')) {
            $sort = request('sort');
            if ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sort === 'role') {
                $query->orderBy('is_super_admin', 'desc')->orderBy('is_admin', 'desc')->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15);
        
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'users' => $users->items(),
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage()
            ]);
        }

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_super_admin) {
            abort(403, 'Acceso no autorizado. Solo el superadministrador puede realizar esta acción.');
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_super_admin) {
            abort(403, 'Acceso no autorizado. Solo el superadministrador puede realizar esta acción.');
        }

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s.@]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? true : false,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Usuario creado correctamente', 'user' => $user]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente');

    }

    public function edit(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Verificar si se puede editar este usuario
        if (!$user->canBeModifiedBy(auth()->user())) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'No tienes permiso para editar este usuario'], 403);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para editar este usuario');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Verificar si se puede modificar este usuario
        if (!$user->canBeModifiedBy(auth()->user())) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'No tienes permiso para modificar este usuario'], 403);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para modificar este usuario');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Solo super admin puede cambiar roles
        if (auth()->user()->isSuperAdmin()) {
            if ($request->has('is_admin')) {
                $data['is_admin'] = (bool) $request->is_admin;
            }
        }

        $user->update($data);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Usuario actualizado correctamente', 'user' => $user->fresh()]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Verificar si se puede eliminar este usuario
        if (!$user->canBeDeletedBy(auth()->user())) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'No tienes permiso para eliminar este usuario'], 403);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para eliminar este usuario');
        }



        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Usuario eliminado correctamente']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    public function toggleAdmin(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Solo super admin puede cambiar roles
        if (!auth()->user()->isSuperAdmin()) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Solo el Super Admin puede cambiar roles de administrador'], 403);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede cambiar roles de administrador');
        }

        // No permitir cambiar el rol del super admin
        if ($user->isSuperAdmin()) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'No puedes modificar al Super Admin'], 400);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes modificar al Super Admin');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'convertido en administrador' : 'quitado como administrador';
        
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => "Usuario {$status} correctamente"]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$status} correctamente");
    }

    /**
     * Banear un usuario
     */
    public function ban(Request $request, User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        if ($user->isSuperAdmin()) {
            if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'No puedes banear al Super Admin'], 400);
            return redirect()->route('admin.users.index')->with('error', 'No puedes banear al Super Admin');
        }

        if ($user->is_admin && !auth()->user()->isSuperAdmin()) {
            if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'No tienes permiso para banear a otros administradores'], 403);
            return redirect()->route('admin.users.index')->with('error', 'No tienes permiso para banear a otros administradores');
        }

        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'No puedes banearte a ti mismo'], 400);
            return redirect()->route('admin.users.index')->with('error', 'No puedes banearte a ti mismo');
        }

        // Validar los datos del formulario (acepta valores numéricos para horas personalizadas)
        $request->validate([
            'reason' => 'required|string|max:255',
            'duration' => 'required', // Puede ser string (permanent, 1, 6, etc) o número
            'type' => 'sometimes|in:account,chat',
        ]);

        // Crear el baneo
        $data = [
            'user_id' => $user->id,
            'banned_by' => auth()->id(),
            'reason' => $request->reason,
            'type' => $request->type ?? 'account',
        ];

        if ($request->duration === 'permanent') {
            $data['is_permanent'] = true;
            $data['banned_until'] = null;
        } else {
            // Si es un número (personalizado) o uno de los predefinidos
            $hours = (int) $request->duration;
            $data['banned_until'] = Carbon::now()->addHours($hours);
        }

        Ban::create($data);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => "Usuario {$user->name} baneado correctamente"]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->name} baneado correctamente");
    }

    /**
     * Desbanear un usuario
     */
    public function unban(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        if ($user->isSuperAdmin()) {
            if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'El Super Admin no puede ser desbaneado'], 400);
            return redirect()->route('admin.users.index')->with('error', 'El Super Admin no puede ser desbaneado');
        }

        if ($user->is_admin && !auth()->user()->isSuperAdmin()) {
            if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'No tienes permiso para desbanear a otros administradores'], 403);
            return redirect()->route('admin.users.index')->with('error', 'No tienes permiso para desbanear a otros administradores');
        }

        // Buscar el baneo activo
        $ban = $user->activeBan();
        
        if ($ban) {
            $ban->update([
                'banned_until' => Carbon::now(),
                'is_permanent' => false
            ]);
            if (request()->wantsJson()) return response()->json(['status' => 'success', 'message' => "Usuario {$user->name} desbaneado correctamente"]);

            return redirect()->route('admin.users.index')->with('success', "Usuario {$user->name} desbaneado correctamente");
        }

        if (request()->wantsJson()) return response()->json(['status' => 'error', 'message' => 'El usuario no está baneado'], 400);
        return redirect()->route('admin.users.index')->with('error', 'El usuario no está baneado');
    }
    /**
     * Actualizar puntos de un usuario (Solo Super Admin)
     */
    public function updatePoints(Request $request, \App\Models\User $user)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Solo el Super Administrador puede gestionar puntos manualmente');
        }

        $request->validate([
            'amount' => 'required|integer|min:-1000000|max:1000000',
        ]);

        $user->points += $request->amount;
        if ($user->points < 0) $user->points = 0;
        $user->save();

        $action = $request->amount >= 0 ? 'añadidos' : 'restados';
        $absAmount = abs($request->amount);

        return response()->json([
            'status' => 'success',
            'message' => "Se han {$action} {$absAmount} puntos a {$user->name}",
            'new_points' => $user->points
        ]);
    }
}
