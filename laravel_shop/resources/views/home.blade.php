<x-store-layout>
    <!-- HERO SECTION - CON TEXTO DESCRIPTIVO -->
    <div class="relative rounded-3xl overflow-hidden mb-12 border border-neon-blue/20 shadow-[0_0_30px_rgba(0,210,255,0.1)]">
        <div class="h-[500px] bg-gradient-to-br from-neon-blue/10 via-neon-purple/10 to-neon-red/10 flex items-center justify-center px-4 relative z-10">
            <div class="max-w-4xl text-center">
                <!-- TÍTULO PRINCIPAL -->
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-black text-white leading-none mb-6 tracking-tighter uppercase italic">
                    <span class="text-neon-blue neon-text-blue block">GAMER</span>
                    <span class="text-neon-purple neon-text-purple block">GUILD</span>
                </h1>
                
                <!-- TEXTO DESCRIPTIVO (restaurado) -->
                <p class="text-lg sm:text-xl text-gray-300 mb-8 leading-relaxed font-medium max-w-3xl mx-auto">
                    Tu santuario definitivo para la cultura gamer y otaku. En Gamer Guild nos apasiona ofrecerte lo último en videojuegos, manga de colección, las figuras más detalladas y el mejor cosplay para tus eventos. ¡Únete a nuestra hermandad!
                </p>
                
                <!-- BOTONES -->
                <div class="flex flex-wrap gap-4 justify-center">
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
        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-red pl-4 mb-8">
            Artículos <span class="text-neon-red">Exclusivos</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
                $exclusivos = App\Models\Product::where('is_exclusive', true)
                    ->where('stock', '>', 0)
                    ->take(4)
                    ->get();
            @endphp
            @forelse($exclusivos as $product)
                @include('products.partials.product-card', ['product' => $product])
            @empty
                <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                    <p class="text-gray-400">No hay artículos exclusivos disponibles</p>
                </div>
            @endforelse
        </div>
    </div>
</x-store-layout>
