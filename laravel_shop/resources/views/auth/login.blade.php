<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - Soul Guild</title>

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
                    <span class="text-neon-blue group-hover:neon-text-blue transition">SOUL</span>
                    <span class="text-neon-purple group-hover:neon-text-purple transition">GUILD</span>
                </h1>
            </a>
            <p class="text-gray-400 mt-2">¡Bienvenido de vuelta, héroe!</p>
        </div>

        <!-- Tarjeta de login -->
        <div class="relative z-10 w-full max-w-md">
            <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-8 shadow-2xl backdrop-blur-sm">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-neon-blue to-neon-purple rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(157,0,255,0.3)]">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <h2 class="text-2xl font-black text-center text-white mb-6">INICIAR SESIÓN</h2>

                <!-- Session Status -->
                @if(session('status'))
                    <div class="mb-4 p-3 bg-green-900/50 border border-green-500 rounded-lg text-green-200 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('email') border-neon-red @enderror"
                               placeholder="tu@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Contraseña</label>
                        <input id="password" type="password" name="password" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('password') border-neon-red @enderror"
                               placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                            <span class="ml-2 text-sm text-gray-400">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-neon-purple hover:text-neon-blue transition">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full px-6 py-4 bg-gradient-to-r from-neon-blue to-neon-purple text-white font-black rounded-lg hover:scale-105 transition transform hover:shadow-[0_0_30px_rgba(157,0,255,0.5)]">
                        ENTRAR AL GREMIO
                    </button>

                    <!-- Register link -->
                    <div class="text-center text-gray-400">
                        ¿No eres miembro aún?
                        <a href="{{ route('register') }}" class="text-neon-purple hover:text-neon-blue font-bold transition">
                            Únete al gremio
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer pequeño -->
        <div class="relative z-10 mt-8 text-center text-gray-600 text-sm">
            &copy; {{ date('Y') }} Soul Guild. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
