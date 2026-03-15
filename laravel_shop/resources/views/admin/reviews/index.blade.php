<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-yellow-500">⭐ Gestión de Valoraciones</span>
                    </h1>
                    <p class="text-gray-400">Administra las valoraciones de los productos</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Ver Productos
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Ver Pedidos
                    </a>
                </div>
            </div>

            <!-- Resto del contenido igual... -->
            @include('admin.reviews.partials.list')
        </div>
    </div>
</x-store-layout>
