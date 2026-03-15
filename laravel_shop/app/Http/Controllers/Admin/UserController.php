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

        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
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
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para modificar este usuario');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Solo super admin puede cambiar roles
        if (auth()->user()->isSuperAdmin()) {
            $data['is_admin'] = $request->has('is_admin') ? true : false;
        }

        $user->update($data);

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
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para eliminar este usuario');
        }

        // No permitir eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminarte a ti mismo');
        }

        $user->delete();

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
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede cambiar roles de administrador');
        }

        // No permitir cambiar el rol del super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes modificar al Super Admin');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'convertido en administrador' : 'quitado como administrador';
        
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

        // Verificaciones de seguridad
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes banear al Super Admin');
        }

        if ($user->is_admin && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para banear a otros administradores');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes banearte a ti mismo');
        }

        // Validar los datos del formulario (acepta valores numéricos para horas personalizadas)
        $request->validate([
            'reason' => 'required|string|max:255',
            'duration' => 'required', // Puede ser string (permanent, 1, 6, etc) o número
        ]);

        // Crear el baneo
        $data = [
            'user_id' => $user->id,
            'banned_by' => auth()->id(),
            'reason' => $request->reason,
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

        // Verificaciones de seguridad
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'El Super Admin no puede ser desbaneado');
        }

        if ($user->is_admin && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No tienes permiso para desbanear a otros administradores');
        }

        // Buscar el baneo activo
        $ban = $user->activeBan();
        
        if ($ban) {
            $ban->update(['banned_until' => Carbon::now()]);
            return redirect()->route('admin.users.index')
                ->with('success', "Usuario {$user->name} desbaneado correctamente");
        }

        return redirect()->route('admin.users.index')
            ->with('error', 'El usuario no está baneado');
    }
}