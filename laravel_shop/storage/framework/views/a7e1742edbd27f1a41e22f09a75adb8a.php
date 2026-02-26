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
        .neon-text-red { text-shadow: 0 0 5px #ff0055, 0 0 10px #ff0055; }
    </style>
</head>
<body class="font-sans antialiased bg-gamer-dark text-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header Fijo -->
        <header class="sticky top-0 z-50 bg-gamer-dark/90 backdrop-blur-sm border-b border-neon-purple/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo (único enlace a inicio) -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo e(route('home')); ?>" class="flex items-center group">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Gamer Guild Logo" class="h-16 w-auto transition-transform group-hover:scale-110">
                            <span class="ml-3 text-2xl font-black tracking-tighter text-white group-hover:text-neon-blue transition">
                                GAMER <span class="text-neon-purple">GUILD</span>
                            </span>
                        </a>
                    </div>

                    <!-- Menú con categorías (sin inicio duplicado) -->
                    <nav class="hidden md:flex space-x-6 items-center">
                        <a href="<?php echo e(route('products.category', 'consolas')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->is('products/category/consolas') ? 'text-neon-blue' : ''); ?>">Consolas</a>
                        <a href="<?php echo e(route('products.category', 'videojuegos')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->is('products/category/videojuegos') ? 'text-neon-blue' : ''); ?>">Videojuegos</a>
                        <a href="<?php echo e(route('products.category', 'manga')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->is('products/category/manga') ? 'text-neon-blue' : ''); ?>">Manga</a>
                        <a href="<?php echo e(route('products.category', 'productos-anime')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->is('products/category/productos-anime') ? 'text-neon-blue' : ''); ?>">Productos Anime</a>
                        <a href="<?php echo e(route('products.category', 'cosplay')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->is('products/category/cosplay') ? 'text-neon-blue' : ''); ?>">Cosplay</a>
                        <a href="<?php echo e(route('offers')); ?>" class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->routeIs('offers') ? 'text-neon-blue' : ''); ?>">Ofertas</a>
                    </nav>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2 text-gray-400 hover:text-neon-blue transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <?php if(session('cart')): ?>
                                <span class="absolute top-0 right-0 bg-neon-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php echo e(count(session('cart'))); ?></span>
                            <?php endif; ?>
                        </a>

                        <?php if(auth()->guard()->check()): ?>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-bold text-gray-300 hover:text-white transition">
                                    <?php echo e(Auth::user()->name); ?>

                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-gamer-card border border-gray-700 rounded-md shadow-xl py-1 z-50">
                                    <?php if(Auth::user()->is_admin): ?>
                                        <a href="<?php echo e(route('admin.products.index')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">👑 Panel Admin</a>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-red">
                                            Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="text-sm font-bold text-gray-400 hover:text-neon-blue transition">Entrar</a>
                            <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-neon-purple text-white text-sm font-bold rounded hover:bg-neon-purple/80 transition">Registro</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENEDOR PRINCIPAL CON IMÁGENES LATERALES -->
        <div class="relative flex-grow">
            <!-- FONDO IZQUIERDO - Imagen anime -->
            <div class="fixed left-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden">
                <img src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1887&auto=format&fit=crop" 
                     alt="Anime Collection" 
                     class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent to-gamer-dark"></div>
            </div>

            <!-- FONDO DERECHO - Imagen gaming -->
            <div class="fixed right-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden">
                <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop" 
                     alt="Gaming Setup" 
                     class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-l from-transparent to-gamer-dark"></div>
            </div>

            <!-- CONTENIDO CENTRAL -->
            <div class="relative z-10 min-h-screen">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <?php if(session('success')): ?>
                        <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-6">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php echo e($slot); ?>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black/80 border-t border-gray-800 py-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    &copy; <?php echo e(date('Y')); ?> Gamer Guild. Todos los derechos reservados.
                </div>
            </div>
        </footer>
    </div>

    <!-- CHAT FLOTANTE -->
    <?php echo $__env->make('components.floating-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/components/store-layout.blade.php ENDPATH**/ ?>