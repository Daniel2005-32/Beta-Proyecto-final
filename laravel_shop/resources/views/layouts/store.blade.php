<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'GameAnimeStore') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Estilos adicionales para el tema gaming -->
    <style>
        /* Aquí puedes agregar estilos personalizados si es necesario */
        .neon-text-blue {
            text-shadow: 0 0 10px rgba(0, 210, 255, 0.7);
        }
        .neon-text-purple {
            text-shadow: 0 0 10px rgba(157, 0, 255, 0.7);
        }
        .neon-text-red {
            text-shadow: 0 0 10px rgba(255, 0, 85, 0.7);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gamer-dark text-white">
    <!-- Navbar de la tienda -->
    <nav class="bg-gamer-card border-b border-neon-blue/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-black uppercase tracking-tighter">
                        <span class="text-neon-blue">Game</span>
                        <span class="text-neon-purple">Anime</span>
                        <span class="text-white">Store</span>
                    </a>
                </div>

                <!-- Menú de navegación con categorías -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->routeIs('home') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Inicio
                    </a>
                    
                    <!-- Dropdown de Productos -->
                    <div class="relative group">
                        <a href="{{ route('products.index') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->routeIs('products.*') ? 'text-neon-blue' : 'text-gray-300' }} inline-flex items-center">
                            Productos
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                        
                        <!-- Submenú desplegable de categorías -->
                        <div class="absolute left-0 mt-2 w-56 bg-gamer-card border border-neon-blue/20 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 py-2">
                            <a href="{{ route('products.category', 'consolas') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-neon-blue/10 hover:text-neon-blue transition">
                                Consolas
                            </a>
                            <a href="{{ route('products.category', 'videojuegos') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-neon-blue/10 hover:text-neon-blue transition">
                                Videojuegos
                            </a>
                            <a href="{{ route('products.category', 'manga') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-neon-blue/10 hover:text-neon-blue transition">
                                Manga
                            </a>
                            <a href="{{ route('products.category', 'productos-anime') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-neon-blue/10 hover:text-neon-blue transition">
                                Productos Anime
                            </a>
                            <div class="border-t border-neon-blue/20 my-2"></div>
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-neon-purple hover:bg-neon-purple/10 hover:text-neon-purple transition font-bold">
                                Ver todos
                            </a>
                        </div>
                    </div>

                    <!-- Categorías principales como enlaces directos (opcional) -->
                    <a href="{{ route('products.category', 'consolas') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->is('products/category/consolas') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Consolas
                    </a>
                    <a href="{{ route('products.category', 'videojuegos') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->is('products/category/videojuegos') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Videojuegos
                    </a>
                    <a href="{{ route('products.category', 'manga') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->is('products/category/manga') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Manga
                    </a>
                    <a href="{{ route('products.category', 'productos-anime') }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->is('products/category/productos-anime') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Productos Anime
                    </a>

                    <!-- Ofertas -->
                    <a href="{{ route('offers') ?? '#' }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->routeIs('offers') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Ofertas
                    </a>
                    
                    <!-- Contacto -->
                    <a href="{{ route('contact') ?? '#' }}" class="text-sm font-bold uppercase tracking-wider hover:text-neon-blue transition {{ request()->routeIs('contact') ? 'text-neon-blue' : 'text-gray-300' }}">
                        Contacto
                    </a>
                </div>

                <!-- Versión móvil del menú (hamburguesa) -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-300 hover:text-neon-blue focus:outline-none" id="mobile-menu-button">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- User Menu / Login -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-bold uppercase tracking-wider bg-neon-blue/20 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold uppercase tracking-wider bg-neon-purple/20 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition">
                            Registro
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Menú móvil (oculto por defecto) -->
    <div class="hidden md:hidden bg-gamer-card border-b border-neon-blue/20" id="mobile-menu">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Inicio
            </a>
            <a href="{{ route('products.index') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Todos los Productos
            </a>
            <a href="{{ route('products.category', 'consolas') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Consolas
            </a>
            <a href="{{ route('products.category', 'videojuegos') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Videojuegos
            </a>
            <a href="{{ route('products.category', 'manga') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Manga
            </a>
            <a href="{{ route('products.category', 'productos-anime') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Productos Anime
            </a>
            <a href="{{ route('offers') ?? '#' }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Ofertas
            </a>
            <a href="{{ route('contact') ?? '#' }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                Contacto
            </a>
            
            @auth
                <div class="border-t border-neon-blue/20 pt-2 mt-2">
                    <a href="{{ route('dashboard') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-neon-blue hover:text-neon-blue/80 transition">
                        Dashboard
                    </a>
                </div>
            @else
                <div class="border-t border-neon-blue/20 pt-2 mt-2 space-y-2">
                    <a href="{{ route('login') }}" class="block py-2 text-sm font-bold uppercase tracking-wider text-gray-300 hover:text-neon-blue transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="block py-2 text-sm font-bold uppercase tracking-wider bg-neon-purple/20 text-neon-purple rounded-lg text-center hover:bg-neon-purple hover:text-white transition">
                        Registro
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gamer-card border-t border-neon-blue/20 mt-20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-neon-blue font-bold mb-4">GameAnimeStore</h3>
                    <p class="text-gray-400 text-sm">Tu tienda de confianza para videojuegos, consolas, manga y productos anime.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Categorías</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('products.category', 'consolas') }}" class="hover:text-neon-blue transition">Consolas</a></li>
                        <li><a href="{{ route('products.category', 'videojuegos') }}" class="hover:text-neon-blue transition">Videojuegos</a></li>
                        <li><a href="{{ route('products.category', 'manga') }}" class="hover:text-neon-blue transition">Manga</a></li>
                        <li><a href="{{ route('products.category', 'productos-anime') }}" class="hover:text-neon-blue transition">Productos Anime</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-neon-blue transition">Inicio</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-neon-blue transition">Productos</a></li>
                        <li><a href="{{ route('offers') ?? '#' }}" class="hover:text-neon-blue transition">Ofertas</a></li>
                        <li><a href="{{ route('contact') ?? '#' }}" class="hover:text-neon-blue transition">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Síguenos</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-neon-blue transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-neon-blue transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-neon-blue transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-neon-blue/20 mt-8 pt-8 text-center">
                <p class="text-gray-400 text-sm">© {{ date('Y') }} GameAnimeStore. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Script para el menú móvil -->
    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        });
    </script>
</body>
</html>