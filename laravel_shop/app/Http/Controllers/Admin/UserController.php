<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios
     */
    public function index()
    {
        // Verificación manual de admin
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        return view('admin.users.create');
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'is_admin' => 'sometimes|boolean'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin')
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function edit(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'sometimes|boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin')
        ];

        // Si se proporciona nueva contraseña
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        // Evitar que el admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Alternar estado de admin
     */
    public function toggleAdmin(User $user)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Acceso no autorizado');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success', 'Rol de administrador actualizado');
    }
}
