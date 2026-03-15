<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-green">📦 Gestión de Pedidos</span>
                    </h1>
                    <p class="text-gray-400">Administra todos los pedidos de la tienda</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Ver Productos
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-yellow-600/10 text-yellow-500 rounded-lg hover:bg-yellow-600 hover:text-white transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Ver Valoraciones
                    </a>
                </div>
            </div>

            <!-- Resto del contenido igual... -->
            @include('admin.orders.partials.list')
        </div>
    </div>
</x-store-layout>
