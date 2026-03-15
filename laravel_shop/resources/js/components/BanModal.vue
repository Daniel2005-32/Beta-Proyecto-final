<template>
  <div>
    <!-- Modal overlay -->
    <div v-if="show" class="fixed inset-0 z-50" @click.self="close">
      <!-- Fondo oscuro -->
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="close"></div>

      <!-- Modal -->
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-gamer-card rounded-2xl border border-neon-red/30 max-w-md w-full p-6">
          <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-neon-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
            Banear a <span class="text-neon-red">{{ userName }}</span>
          </h2>

          <form :action="`/admin/users/${userId}/ban`" method="POST">
            <input type="hidden" name="_token" :value="csrfToken">
            
            <div class="space-y-4">
              <div>
                <label class="block text-gray-300 mb-2 font-bold">Razón del baneo</label>
                <textarea name="reason" rows="3" required
                          placeholder="Motivo por el que se banea al usuario..."
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red"></textarea>
              </div>

              <div>
                <label class="block text-gray-300 mb-2 font-bold">Duración</label>
                <div class="grid grid-cols-2 gap-2">
                  <label class="flex items-center space-x-2 p-2 bg-gray-800 rounded cursor-pointer hover:bg-gray-700">
                    <input type="radio" name="duration" value="permanent" class="text-neon-red">
                    <span class="text-white text-sm">Permanente</span>
                  </label>
                  <label class="flex items-center space-x-2 p-2 bg-gray-800 rounded cursor-pointer hover:bg-gray-700">
                    <input type="radio" name="duration" value="1" class="text-neon-red">
                    <span class="text-white text-sm">1 hora</span>
                  </label>
                  <label class="flex items-center space-x-2 p-2 bg-gray-800 rounded cursor-pointer hover:bg-gray-700">
                    <input type="radio" name="duration" value="6" class="text-neon-red">
                    <span class="text-white text-sm">6 horas</span>
                  </label>
                  <label class="flex items-center space-x-2 p-2 bg-gray-800 rounded cursor-pointer hover:bg-gray-700">
                    <input type="radio" name="duration" value="24" class="text-neon-red">
                    <span class="text-white text-sm">24 horas</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="flex gap-3 mt-6">
              <button type="button" 
                      @click="close"
                      class="flex-1 px-4 py-2 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition">
                Cancelar
              </button>
              <button type="submit" 
                      class="flex-1 px-4 py-2 bg-neon-red text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(255,0,85,0.4)]">
                Banear
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      default: false
    },
    userId: {
      type: Number,
      default: null
    },
    userName: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      csrfToken: document.querySelector('meta[name="csrf-token"]').content
    }
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>
