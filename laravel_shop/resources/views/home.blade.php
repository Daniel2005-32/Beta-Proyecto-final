<x-store-layout>
    <!-- Sección de Bienvenida (Intro) -->
    <div class="relative rounded-3xl overflow-hidden mb-12 border border-neon-blue/20 bg-gamer-card shadow-[0_0_30px_rgba(0,210,255,0.1)]">
        <div class="absolute inset-0 bg-gradient-to-r from-gamer-dark via-transparent to-gamer-dark opacity-60"></div>
        <!-- Simulación de Banner -->
        <div class="h-[400px] bg-gradient-to-br from-neon-blue/10 via-neon-purple/10 to-neon-red/10 flex items-center px-8 md:px-16 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-4 tracking-tighter uppercase italic">
                    Bienvenidos a <span class="text-neon-blue neon-text-blue">Gamer</span> <span class="text-neon-purple neon-text-purple">Guild</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 leading-relaxed font-medium">
                    Tu santuario definitivo para la cultura gamer y otaku. En Gamer Guild nos apasiona ofrecerte lo último en videojuegos, manga de colección, las figuras más detalladas y el mejor cosplay para tus eventos. ¡Únete a nuestra hermandad!
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black uppercase tracking-widest rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        Explorar Catálogo
                    </a>
                    <a href="{{ route('auctions.index') }}" class="px-8 py-4 border-2 border-neon-purple text-neon-purple font-black uppercase tracking-widest rounded-full hover:bg-neon-purple hover:text-white transition shadow-[0_0_20px_rgba(157,0,255,0.2)]">
                        Ver Subastas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Destacados -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-blue pl-4">
                Productos <span class="text-neon-blue">Destacados</span>
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-gray-500 hover:text-neon-blue transition uppercase tracking-widest">Ver todo</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featured as $product)
                @include('products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

    <!-- Tendencias -->
    <div class="mb-16">
        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-purple pl-4 mb-8">
            En <span class="text-neon-purple">Tendencia</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($trending as $product)
                @include('products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    
    <!-- Artículos Exclusivos -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-red pl-4">
                Artículos <span class="text-neon-red">Exclusivos</span>
            </h2>
            <a href="{{ route('products.exclusivos') }}" class="text-sm font-bold text-gray-500 hover:text-neon-red transition uppercase tracking-widest">Ver todos</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($exclusive as $product)
                @include('products.partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                    <p class="text-gray-400">No hay artículos exclusivos disponibles</p>
                </div>
            @endforelse
        </div>
    </div>
</x-store-layout>
