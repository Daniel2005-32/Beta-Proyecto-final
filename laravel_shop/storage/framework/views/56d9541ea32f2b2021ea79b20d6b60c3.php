<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Gamer Guild')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <style>
        .neon-text-blue { text-shadow: 0 0 5px #00d2ff, 0 0 10px #00d2ff; }
        .neon-text-purple { text-shadow: 0 0 5px #9d00ff, 0 0 10px #9d00ff; }
        .neon-border-blue { box-shadow: 0 0 5px #00d2ff, inset 0 0 5px #00d2ff; }
        .nav-link-hover:hover {
            color: #00d2ff;
            text-shadow: 0 0 8px #00d2ff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gamer-dark text-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header Fijo -->
        <header class="sticky top-0 z-50 bg-gamer-dark/90 backdrop-blur-sm border-b border-neon-purple/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo e(route('home')); ?>" class="flex items-center group">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Gamer Guild Logo" class="h-16 w-auto transition-transform group-hover:scale-110">
                            <span class="ml-3 text-2xl font-black tracking-tighter text-white group-hover:text-neon-blue transition">
                                GAMER <span class="text-neon-purple">GUILD</span>
                            </span>
                        </a>
                    </div>

                    <!-- Navegación -->
                    <nav class="hidden md:flex space-x-6 items-center">
                        <a href="<?php echo e(route('products.index', ['category' => 'videojuegos'])); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover">Videojuegos</a>
                        <a href="<?php echo e(route('products.index', ['category' => 'manga'])); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover">Manga</a>
                        <a href="<?php echo e(route('products.index', ['category' => 'figuras'])); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover">Figuras de Anime</a>
                        <a href="<?php echo e(route('products.index', ['category' => 'productos-anime'])); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover">Productos de Anime</a>
                        <a href="<?php echo e(route('products.index', ['category' => 'cosplay'])); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover">Cosplay</a>
                    </nav>

                    <!-- Acciones Derecha -->
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2 text-gray-400 hover:text-neon-blue transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <?php if(session('cart')): ?>
                                <span class="absolute top-0 right-0 bg-neon-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-[0_0_10px_#ff0055]">
                                    <?php echo e(count(session('cart'))); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <?php if(auth()->guard()->check()): ?>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-bold text-gray-300 hover:text-white transition">
                                    <?php echo e(Auth::user()->name); ?>

                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-gamer-card border border-gray-700 rounded-md shadow-xl py-1 z-50">
                                    <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">Panel de Control</a>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-red">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="text-sm font-bold text-gray-400 hover:text-neon-blue transition">Entrar</a>
                            <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-neon-purple text-white text-sm font-bold rounded hover:bg-neon-purple/80 transition shadow-[0_0_15px_rgba(157,0,255,0.4)]">Registro</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="flex-grow py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php if(session('success')): ?>
                    <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-6 backdrop-blur-sm">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <?php echo e($slot); ?>

            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-black/80 border-t border-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="h-10 w-auto">
                        <span class="ml-2 text-xl font-bold tracking-tighter">GAMER <span class="text-neon-purple">GUILD</span></span>
                    </div>
                    <p class="text-gray-500 text-sm max-w-xs">
                        Tu santuario definitivo para todo lo relacionado con videojuegos, anime y cultura geek. Únete a la hermandad.
                    </p>
                </div>
                <div>
                    <h3 class="text-neon-blue font-bold uppercase tracking-widest text-sm mb-4">Enlaces</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Sobre nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition">Términos y condiciones</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacidad</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-neon-purple font-bold uppercase tracking-widest text-sm mb-4">Redes Sociales</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-neon-blue transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-900 text-center text-xs text-gray-600">
                &copy; <?php echo e(date('Y')); ?> Gamer Guild. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</body>
</html><?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/components/store-layout.blade.php ENDPATH**/ ?>