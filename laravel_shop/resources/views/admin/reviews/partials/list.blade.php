<div class="bg-gamer-card rounded-2xl border border-yellow-500/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-yellow-500/20">
            <tr>
                <th class="px-6 py-4 text-left text-yellow-500">ID</th>
                <th class="px-6 py-4 text-left text-yellow-500">Producto</th>
                <th class="px-6 py-4 text-left text-yellow-500">Usuario</th>
                <th class="px-6 py-4 text-left text-yellow-500">Valoración</th>
                <th class="px-6 py-4 text-left text-yellow-500">Comentario</th>
                <th class="px-6 py-4 text-left text-yellow-500">Estado</th>
                <th class="px-6 py-4 text-left text-yellow-500">Fecha</th>
                <th class="px-6 py-4 text-left text-yellow-500">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">{{ $review->id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('products.show', $review->product->slug) }}" class="text-neon-blue hover:underline">
                            {{ $review->product->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-white">{{ $review->user->name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-300 max-w-xs truncate">{{ $review->comment ?: 'Sin comentario' }}</td>
                    <td class="px-6 py-4">
                        @if($review->is_approved)
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Aprobada</span>
                        @else
                            <span class="px-3 py-1 bg-yellow-600/20 text-yellow-400 rounded-full text-xs">Pendiente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $review->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600/10 text-green-400 rounded-lg hover:bg-green-600 hover:text-white transition text-sm">
                                        Aprobar
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta valoración?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay valoraciones para mostrar</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $reviews->links() }}
</div>
