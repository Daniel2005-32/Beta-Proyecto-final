<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Soul Guild')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <style>
        .neon-text-blue { text-shadow: 0 0 5px #00d2ff, 0 0 10px #00d2ff; }
        .neon-text-purple { text-shadow: 0 0 5px #9d00ff, 0 0 10px #9d00ff; }
        .neon-text-red { text-shadow: 0 0 5px #ff0055, 0 0 10px #ff0055; }
        /* Para pantallas muy grandes */
        @media (min-width: 2000px) {
            .max-w-screen-2xl {
                max-width: 95% !important;
            }
            
            .grid {
                grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gamer-dark text-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header Fijo -->
        <header class="sticky top-0 z-50 bg-gamer-dark/90 backdrop-blur-sm border-b border-neon-purple/30">
            <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center mr-8 lg:mr-12">
                        <a href="<?php echo e(route('home')); ?>" class="flex items-center group">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Soul Guild Logo" class="h-16 w-auto transition-transform group-hover:scale-110">
                            <span class="ml-3 text-2xl font-black tracking-tighter text-white group-hover:text-neon-blue transition">
                                SOUL <span class="text-neon-purple">GUILD</span>
                            </span>
                        </a>
                    </div>

                   <!-- MENÚ PRINCIPAL - SIEMPRE VISIBLE -->
                    <nav class="flex space-x-8 items-center ml-4">
                        <a href="<?php echo e(route('products.index')); ?>" 
                        class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->routeIs('products.*') ? 'text-neon-blue' : ''); ?>">
                            Catálogo
                        </a>
                        <a href="<?php echo e(route('auctions.index')); ?>" 
                        class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->routeIs('auctions.*') ? 'text-neon-purple' : ''); ?>">
                            Subastas
                        </a>
                        <a href="<?php echo e(route('raffles.index')); ?>" 
                        class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->routeIs('raffles.*') ? 'text-neon-purple' : ''); ?>">
                            Sorteos
                        </a>
                        <a href="<?php echo e(route('offers')); ?>" 
                        class="text-sm font-bold uppercase tracking-wider transition nav-link-hover <?php echo e(request()->routeIs('offers') ? 'text-neon-blue' : ''); ?>">
                            Ofertas
                        </a>
                    </nav>

                    <!-- BARRA DE BÚSQUEDA -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <form action="<?php echo e(route('search')); ?>" method="GET" class="relative" id="search-form">
                            <input type="text" 
                                   name="q" 
                                   id="search-input"
                                   placeholder="Buscar productos..." 
                                   value="<?php echo e(request('q')); ?>"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-full pl-6 pr-14 py-3 text-white focus:outline-none focus:border-neon-blue transition text-base"
                                   autocomplete="off">
                            <button type="submit" class="absolute right-4 top-3 text-gray-400 hover:text-neon-blue transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            
                            <!-- Sugerencias de búsqueda -->
                            <div id="search-suggestions" class="absolute top-full left-0 right-0 mt-1 bg-gamer-card border border-neon-blue/20 rounded-lg shadow-xl hidden"></div>
                        </form>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-4">
                        <?php if(auth()->guard()->check()): ?>
                            <?php
                                $isBanned = Auth::user()->isBanned();
                            ?>
                            
                            <!-- Carrito -->
                            <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2 text-gray-400 hover:text-neon-blue transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <?php if(session('cart')): ?>
                                    <span class="absolute top-0 right-0 bg-neon-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php echo e(count(session('cart'))); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <?php if(auth()->guard()->check()): ?>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-bold text-gray-300 hover:text-white transition">
                                    <?php echo e(Auth::user()->name); ?>

                                    <?php if($isBanned): ?>
                                        <span class="ml-2 px-2 py-1 bg-neon-red/20 text-neon-red text-xs rounded-full">BANEADO</span>
                                    <?php endif; ?>
                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-gamer-card border border-gray-700 rounded-md shadow-xl py-1 z-50">
                                    <a href="<?php echo e(route('profile.index')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">👤 Mi Perfil</a>
                                    
                                    <?php if(Auth::user()->is_admin && !$isBanned): ?>
                                        <div class="border-t border-gray-800 my-1"></div>
                                        <div class="px-4 py-1 text-xs text-gray-500 uppercase tracking-wider">Administración</div>
                                        <a href="<?php echo e(route('admin.products.index')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">📦 Productos</a>
                                        <a href="<?php echo e(route('admin.users.index')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">👥 Usuarios</a>
                                        <a href="<?php echo e(route('admin.raffles.index')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-blue">🎲 Sorteos</a>
                                        
                                        <!-- LIMPIAR CHAT - VERSIÓN QUE FUNCIONA CON JSON -->
                                        <div class="border-t border-gray-800 my-1"></div>
                                        <div class="px-4 py-1 text-xs text-gray-500 uppercase tracking-wider">Utilidades</div>

                                        <button onclick="cleanChat()" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-neon-red transition flex items-center gap-2" id="clean-chat-button">
                                            <span id="clean-chat-icon">🧹</span>
                                            <span id="clean-chat-text">Limpiar chat</span>
                                        </button>

                                        <script>
                                        function cleanChat() {
                                            const button = document.getElementById('clean-chat-button');
                                            const icon = document.getElementById('clean-chat-icon');
                                            const text = document.getElementById('clean-chat-text');
                                            
                                            // Cambiar estado a "limpiando"
                                            icon.innerHTML = '⏳';
                                            text.innerHTML = 'Limpiando...';
                                            button.disabled = true;
                                            button.classList.add('opacity-50', 'cursor-not-allowed');
                                            
                                            // Enviar petición AJAX
                                            fetch('<?php echo e(route("admin.clean-messages")); ?>', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    icon.innerHTML = '✅';
                                                    text.innerHTML = '¡Chat limpio!';
                                                    
                                                    // Recargar la página después de 1 segundo para ver los cambios
                                                    setTimeout(() => {
                                                        location.reload();
                                                    }, 1000);
                                                } else {
                                                    icon.innerHTML = '❌';
                                                    text.innerHTML = 'Error';
                                                    
                                                    setTimeout(() => {
                                                        icon.innerHTML = '🧹';
                                                        text.innerHTML = 'Limpiar chat';
                                                        button.disabled = false;
                                                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                                                    }, 2000);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                icon.innerHTML = '❌';
                                                text.innerHTML = 'Error';
                                                
                                                setTimeout(() => {
                                                    icon.innerHTML = '🧹';
                                                    text.innerHTML = 'Limpiar chat';
                                                    button.disabled = false;
                                                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                                                }, 2000);
                                            });
                                        }
                                        </script>
                                    <?php endif; ?>
                                    
                                    <div class="border-t border-gray-800 my-1"></div>
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

        <!-- Banner de baneo -->
        <?php if(auth()->guard()->check()): ?>
            <?php
                $ban = Auth::user()->activeBan();
            ?>
            <?php if(Auth::user()->isBanned() && $ban): ?>
                <div class="bg-neon-red/20 border-b border-neon-red/30 py-3">
                    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-neon-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span class="text-white font-bold">
                                    Has sido baneado: <?php echo e($ban->reason); ?>

                                </span>
                                <?php if(!$ban->is_permanent): ?>
                                    <span class="text-gray-300 text-sm">
                                        Tiempo restante: <?php echo e($ban->timeLeft()); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-neon-red text-sm font-bold">🚫 ACCESO RESTRINGIDO</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="relative flex-grow">
            <!-- FONDO IZQUIERDO -->
            <div class="fixed left-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden">
                <img id="leftImage" 
                     src="https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1887&auto=format&fit=crop" 
                     alt="Anime Collection" 
                     class="w-full h-full object-cover opacity-30 transition-opacity duration-1000">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent to-gamer-dark"></div>
            </div>

            <!-- FONDO DERECHO -->
            <div class="fixed right-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden">
                <img id="rightImage" 
                     src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop" 
                     alt="Gaming Setup" 
                     class="w-full h-full object-cover opacity-30 transition-opacity duration-1000">
                <div class="absolute inset-0 bg-gradient-to-l from-transparent to-gamer-dark"></div>
            </div>

            <!-- CONTENIDO CENTRAL -->
            <div class="relative z-10 min-h-screen">
                <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <?php if(session('success')): ?>
                        <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-6">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="bg-red-900/50 border border-neon-red text-red-200 px-4 py-3 rounded mb-6">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php echo e($slot); ?>

                </div>
            </div>

        <!-- Footer -->
        <footer class="bg-black/80 border-t border-gray-800 py-12 relative z-10">
            <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-gray-500 text-sm">
                    &copy; <?php echo e(date('Y')); ?> Soul Guild. Todos los derechos reservados.
                </div>
            </div>
        </footer>
    </div>

    <!-- CHAT FLOTANTE -->
    <?php if(auth()->guard()->check()): ?>
        <?php if(!Auth::user()->isBanned()): ?>
            <?php echo $__env->make('components.floating-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    <?php else: ?>
        <?php echo $__env->make('components.floating-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <script>
        // Array de imágenes para rotación
        const leftImages = [
            'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1887&auto=format&fit=crop',
            'https://i.pinimg.com/736x/bc/a3/80/bca380011a5a682a9e7766c1d7c2db82.jpg',
            'https://tienda-dragon-ball.com/wp-content/uploads/2024/08/figura-de-goku-ultra-instinto-1.webp',
            'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1887&auto=format&fit=crop'
        ];

        const rightImages = [
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop',
            'https://periodismo.ull.es/wp-content/uploads/2022/04/Se-rumorea-que-Elden-Ring-realizara-proximamente-una-nueva-prueba.jpg',
            'https://cdn1.epicgames.com/offer/e9a679451d094c1ba3d008b6a01adec5/EGS_FINALFANTASYVIIREBIRTH_SquareEnix_S1_2560x1440-e254f978084058f898118dc49728d04c',
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop'
        ];

        let leftIndex = 0;
        let rightIndex = 0;

        function rotateImages() {
            const leftImg = document.getElementById('leftImage');
            leftIndex = (leftIndex + 1) % leftImages.length;
            leftImg.style.opacity = '0.2';
            setTimeout(() => {
                leftImg.src = leftImages[leftIndex];
                leftImg.style.opacity = '0.3';
            }, 500);

            const rightImg = document.getElementById('rightImage');
            rightIndex = (rightIndex + 1) % rightImages.length;
            rightImg.style.opacity = '0.2';
            setTimeout(() => {
                rightImg.src = rightImages[rightIndex];
                rightImg.style.opacity = '0.3';
            }, 500);
        }

        setInterval(rotateImages, 7000);

        // SUGERENCIAS DE BÚSQUEDA
        const searchInput = document.getElementById('search-input');
        const suggestionsDiv = document.getElementById('search-suggestions');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                if (query.length < 2) {
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                fetch(`/buscar/sugerencias?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(product => {
                                html += `
                                    <a href="/products/${product.slug}" class="flex items-center p-3 hover:bg-gray-800 transition border-b border-gray-800 last:border-0">
                                        <img src="${product.image}" alt="${product.name}" class="w-10 h-10 object-cover rounded">
                                        <div class="ml-3 flex-1">
                                            <div class="text-white text-sm font-medium">${product.name}</div>
                                            <div class="text-neon-blue text-xs">${(product.price * 1.21).toFixed(2)}€</div>
                                        </div>
                                    </a>
                                `;
                            });
                            suggestionsDiv.innerHTML = html;
                            suggestionsDiv.classList.remove('hidden');
                        } else {
                            suggestionsDiv.innerHTML = '<div class="p-3 text-gray-400 text-sm">No hay sugerencias</div>';
                            suggestionsDiv.classList.remove('hidden');
                        }
                    });
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/components/store-layout.blade.php ENDPATH**/ ?>