<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registro - Gamer Guild</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .neon-text-blue { text-shadow: 0 0 5px #00d2ff, 0 0 10px #00d2ff; }
        .neon-text-purple { text-shadow: 0 0 5px #9d00ff, 0 0 10px #9d00ff; }
        .neon-text-red { text-shadow: 0 0 5px #ff0055, 0 0 10px #ff0055; }
        .neon-border-blue { box-shadow: 0 0 5px #00d2ff, inset 0 0 5px #00d2ff; }
        .neon-border-purple { box-shadow: 0 0 5px #9d00ff, inset 0 0 5px #9d00ff; }
    </style>
</head>
<body class="font-sans antialiased bg-gamer-dark text-gray-100">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-12 relative overflow-hidden">
        <!-- Fondo gaming -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-neon-blue/5 via-neon-purple/5 to-neon-red/5"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        </div>

        <!-- Logo y título -->
        <div class="relative z-10 text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block group">
                <h1 class="text-5xl font-black tracking-tighter">
                    <span class="text-neon-blue group-hover:neon-text-blue transition">GAMER</span>
                    <span class="text-neon-purple group-hover:neon-text-purple transition">GUILD</span>
                </h1>
            </a>
            <p class="text-gray-400 mt-2">Únete a la hermandad</p>
        </div>

        <!-- Tarjeta de registro -->
        <div class="relative z-10 w-full max-w-md">
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/30 p-8 shadow-2xl backdrop-blur-sm">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-neon-purple to-neon-red rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(255,0,85,0.3)]">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                </div>

                <h2 class="text-2xl font-black text-center text-white mb-6">CREAR CUENTA</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Nombre</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple transition @error('name') border-neon-red @enderror"
                               placeholder="Tu nombre">
                        @error('name')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple transition @error('email') border-neon-red @enderror"
                               placeholder="tu@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Contraseña</label>
                        <input id="password" type="password" name="password" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple transition @error('password') border-neon-red @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Confirmar Contraseña</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple transition"
                               placeholder="••••••••">
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" required
                               class="mt-1 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                        <label for="terms" class="ml-2 text-sm text-gray-400">
                            Acepto los <a href="#" class="text-neon-purple hover:text-neon-blue transition">términos y condiciones</a> 
                            y la <a href="#" class="text-neon-purple hover:text-neon-blue transition">política de privacidad</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full px-6 py-4 bg-gradient-to-r from-neon-purple to-neon-red text-white font-black rounded-lg hover:scale-105 transition transform hover:shadow-[0_0_30px_rgba(255,0,85,0.5)]">
                        UNIRSE AL GREMIO
                    </button>

                    <!-- Login link -->
                    <div class="text-center text-gray-400">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="text-neon-blue hover:text-neon-purple font-bold transition">
                            Iniciar sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer pequeño -->
        <div class="relative z-10 mt-8 text-center text-gray-600 text-sm">
            &copy; {{ date('Y') }} Gamer Guild. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
