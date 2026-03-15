<x-store-layout>
    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-black text-white mb-4">Contacto</h1>
                <p class="text-gray-400">¿Tienes alguna pregunta? Estamos aquí para ayudarte</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Formulario de contacto -->
                <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Envíanos un mensaje</h2>
                    
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-gray-300 mb-2">Nombre</label>
                            <input type="text" name="name" class="w-full bg-gamer-dark border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" class="w-full bg-gamer-dark border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 mb-2">Mensaje</label>
                            <textarea name="message" rows="5" class="w-full bg-gamer-dark border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Enviar mensaje
                        </button>
                    </form>
                </div>
                
                <!-- Información de contacto -->
                <div class="space-y-6">
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                        <h2 class="text-2xl font-bold text-white mb-6">Información de contacto</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="text-neon-purple text-xl">📍</div>
                                <div>
                                    <h3 class="text-white font-bold">Dirección</h3>
                                    <p class="text-gray-400">Calle Principal 123, Madrid, España</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="text-neon-purple text-xl">📞</div>
                                <div>
                                    <h3 class="text-white font-bold">Teléfono</h3>
                                    <p class="text-gray-400">+34 900 123 456</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="text-neon-purple text-xl">✉️</div>
                                <div>
                                    <h3 class="text-white font-bold">Email</h3>
                                    <p class="text-gray-400">info@soulguild.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="text-neon-purple text-xl">⏰</div>
                                <div>
                                    <h3 class="text-white font-bold">Horario</h3>
                                    <p class="text-gray-400">Lunes a Viernes: 10:00 - 20:00</p>
                                    <p class="text-gray-400">Sábados: 11:00 - 18:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
